<?php

namespace App\Support\GoogleDrive;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleDriveClient
{
    public function __construct(private User $user) {}

    public function findOrCreateRootFolder(): string
    {
        if (filled($this->user->google_drive_root_folder_id)) {
            return (string) $this->user->google_drive_root_folder_id;
        }

        $folderName = (string) config('google-drive.root_folder_name', 'Faktury');
        $folderId = $this->findFolderByName($folderName)
            ?? $this->createFolder($folderName);

        $this->user->update(['google_drive_root_folder_id' => $folderId]);

        return $folderId;
    }

    public function findOrCreateFolder(string $name, string $parentId): string
    {
        return $this->findFolderByName($name, $parentId)
            ?? $this->createFolder($name, $parentId);
    }

    public function findFileByName(string $name, string $parentId): ?array
    {
        $query = sprintf(
            "name = '%s' and '%s' in parents and trashed = false",
            $this->escapeQuery($name),
            $parentId
        );

        $response = $this->request('get', 'https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id,name,mimeType,modifiedTime)',
            'pageSize' => 1,
            'spaces' => 'drive',
        ]);

        $files = $response->json('files', []);

        return $files[0] ?? null;
    }

    public function uploadOrUpdateFile(
        string $parentId,
        string $name,
        string $contents,
        string $mimeType,
        ?string $existingFileId = null
    ): string {
        if ($existingFileId !== null) {
            $this->request(
                'patch',
                'https://www.googleapis.com/upload/drive/v3/files/'.$existingFileId,
                [],
                $contents,
                [
                    'uploadType' => 'media',
                ],
                $mimeType
            );

            return $existingFileId;
        }

        $metadata = json_encode([
            'name' => $name,
            'parents' => [$parentId],
        ], JSON_THROW_ON_ERROR);

        $boundary = 'faktury_'.bin2hex(random_bytes(8));
        $body = "--{$boundary}\r\n"
            ."Content-Type: application/json; charset=UTF-8\r\n\r\n"
            .$metadata."\r\n"
            ."--{$boundary}\r\n"
            ."Content-Type: {$mimeType}\r\n\r\n"
            .$contents."\r\n"
            ."--{$boundary}--";

        $response = Http::withToken($this->accessToken())
            ->withHeaders(['Content-Type' => 'multipart/related; boundary='.$boundary])
            ->withBody($body, 'multipart/related; boundary='.$boundary)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id');

        $this->ensureSuccess($response, 'Nepodarilo sa nahrať súbor na Google Drive.');

        return (string) $response->json('id');
    }

    public function findFolder(string $name, string $parentId): ?string
    {
        return $this->findFolderByName($name, $parentId);
    }

    /**
     * @return array<int, array{id: string, name: string, mimeType?: string}>
     */
    public function listChildren(string $parentId): array
    {
        $files = [];
        $pageToken = null;

        do {
            $query = [
                'q' => sprintf("'%s' in parents and trashed = false", $parentId),
                'fields' => 'nextPageToken,files(id,name,mimeType)',
                'pageSize' => 200,
                'spaces' => 'drive',
            ];

            if ($pageToken !== null) {
                $query['pageToken'] = $pageToken;
            }

            $response = $this->request('get', 'https://www.googleapis.com/drive/v3/files', $query);
            $files = array_merge($files, $response->json('files', []));
            $pageToken = $response->json('nextPageToken');
        } while (filled($pageToken));

        return $files;
    }

    public function deleteFile(string $fileId): void
    {
        $this->request('delete', 'https://www.googleapis.com/drive/v3/files/'.$fileId);
    }

    public function deleteFileByName(string $name, string $parentId): void
    {
        $file = $this->findFileByName($name, $parentId);

        if ($file !== null) {
            $this->deleteFile($file['id']);
        }
    }

    public function deleteFileTree(string $fileId): void
    {
        foreach ($this->listChildren($fileId) as $child) {
            if (($child['mimeType'] ?? '') === 'application/vnd.google-apps.folder') {
                $this->deleteFileTree($child['id']);
            } else {
                $this->deleteFile($child['id']);
            }
        }

        $this->deleteFile($fileId);
    }

    public function downloadFile(string $fileId): string
    {
        $response = $this->request(
            'get',
            'https://www.googleapis.com/drive/v3/files/'.$fileId,
            ['alt' => 'media']
        );

        return $response->body();
    }

    private function findFolderByName(string $name, ?string $parentId = null): ?string
    {
        $query = sprintf(
            "mimeType = 'application/vnd.google-apps.folder' and name = '%s' and trashed = false",
            $this->escapeQuery($name)
        );

        if ($parentId !== null) {
            $query .= " and '{$parentId}' in parents";
        }

        $response = $this->request('get', 'https://www.googleapis.com/drive/v3/files', [
            'q' => $query,
            'fields' => 'files(id,name)',
            'pageSize' => 1,
            'spaces' => 'drive',
        ]);

        $files = $response->json('files', []);

        return isset($files[0]['id']) ? (string) $files[0]['id'] : null;
    }

    private function createFolder(string $name, ?string $parentId = null): string
    {
        $metadata = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ];

        if ($parentId !== null) {
            $metadata['parents'] = [$parentId];
        }

        $response = $this->request(
            'post',
            'https://www.googleapis.com/drive/v3/files',
            ['fields' => 'id'],
            json_encode($metadata, JSON_THROW_ON_ERROR),
            [],
            'application/json'
        );

        return (string) $response->json('id');
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $extraQuery
     */
    private function request(
        string $method,
        string $url,
        array $query = [],
        ?string $body = null,
        array $extraQuery = [],
        ?string $contentType = null
    ): Response {
        $request = Http::withToken($this->accessToken());

        if ($contentType !== null) {
            $request = $request->withHeaders(['Content-Type' => $contentType]);
        }

        $fullUrl = $url;

        if ($query !== [] || $extraQuery !== []) {
            $fullUrl .= '?'.http_build_query(array_merge($query, $extraQuery));
        }

        $response = match ($method) {
            'get' => $request->get($fullUrl),
            'post' => $request->withBody($body ?? '', $contentType ?? 'application/json')->post($fullUrl),
            'patch' => $request->withBody($body ?? '', $contentType ?? 'application/json')->patch($fullUrl),
            'delete' => $request->delete($fullUrl),
            default => throw new RuntimeException('Nepodporovaná HTTP metóda.'),
        };

        $this->ensureSuccess($response, 'Google Drive API požiadavka zlyhala.');

        return $response;
    }

    private function accessToken(): string
    {
        if (
            filled($this->user->google_access_token)
            && $this->user->google_token_expires_at !== null
            && $this->user->google_token_expires_at->isFuture()
        ) {
            return (string) $this->user->google_access_token;
        }

        if (! filled($this->user->google_refresh_token)) {
            throw new RuntimeException('Chýba Google refresh token. Odpojte a znova prepojte Google účet.');
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $this->user->google_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $this->ensureSuccess($response, 'Nepodarilo sa obnoviť Google prístupový token.');

        $this->user->update([
            'google_access_token' => $response->json('access_token'),
            'google_token_expires_at' => now()->addSeconds((int) $response->json('expires_in', 3600)),
        ]);

        $this->user->refresh();

        return (string) $this->user->google_access_token;
    }

    private function ensureSuccess(Response $response, string $message): void
    {
        if ($response->successful()) {
            return;
        }

        $detail = trim((string) $response->json('error.message', $response->body()));

        throw new RuntimeException(trim($message.' '.$detail));
    }

    private function escapeQuery(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }
}
