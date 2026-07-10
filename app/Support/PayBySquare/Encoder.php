<?php

namespace App\Support\PayBySquare;

use Illuminate\Support\Facades\Log;

class Encoder
{
    /**
     * @var array<int, string>|null
     */
    private static ?array $cachedCandidates = null;

    public function encode(Payment $payment): ?string
    {
        $tabDelimitedString = $payment->toTabDelimitedString();
        $dataWithHash = $this->addCrc32bHash($tabDelimitedString);
        $compressed = $this->compressWithLzma($dataWithHash);

        if ($compressed === null) {
            Log::warning('PAY by square: nepodarilo sa skomprimovať payload (chýba xz?).');

            return null;
        }

        return $this->convertToBase32($compressed, strlen($dataWithHash));
    }

    private function addCrc32bHash(string $data): string
    {
        return strrev(hash('crc32b', $data, true)).$data;
    }

    private function compressWithLzma(string $data): ?string
    {
        $xzPath = $this->findXzBinary();

        if ($xzPath === null) {
            return null;
        }

        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $command = escapeshellarg($xzPath).' --format=raw --lzma1=lc=3,lp=0,pb=2,dict=128KiB -c -';

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (! is_resource($process)) {
            return null;
        }

        fwrite($pipes[0], $data);
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            Log::warning('PAY by square: xz skončil s chybou.', [
                'exit_code' => $exitCode,
                'stderr' => trim((string) $stderr),
                'binary' => $xzPath,
            ]);

            return null;
        }

        return $compressed ?: null;
    }

    private function findXzBinary(): ?string
    {
        foreach ($this->xzBinaryCandidates() as $path) {
            if ($this->isUsableBinary($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function xzBinaryCandidates(): array
    {
        if (self::$cachedCandidates !== null) {
            return self::$cachedCandidates;
        }

        $configured = config('paybysquare.xz_binary');
        $candidates = [];

        if (filled($configured)) {
            $candidates[] = (string) $configured;
        }

        $candidates = array_merge($candidates, [
            '/usr/bin/xz',
            '/usr/local/bin/xz',
            'C:\\Program Files\\Git\\mingw64\\bin\\xz.exe',
            'C:\\Program Files\\Git\\usr\\bin\\xz.exe',
            'C:\\Program Files\\xz\\xz.exe',
            'C:\\ProgramData\\chocolatey\\bin\\xz.exe',
            'C:\\tools\\xz\\xz.exe',
            'C:\\xz\\xz.exe',
        ]);

        $pathEntries = array_filter(explode(PATH_SEPARATOR, (string) getenv('PATH')));

        foreach ($pathEntries as $directory) {
            $directory = rtrim($directory, '\\/');
            $candidates[] = $directory.'\\xz.exe';
            $candidates[] = $directory.'/xz';
            $candidates[] = $directory.'/xz.exe';
        }

        $fromPathLookup = $this->lookupXzInPath();

        if ($fromPathLookup !== null) {
            $candidates[] = $fromPathLookup;
        }

        self::$cachedCandidates = array_values(array_unique(array_filter($candidates)));

        return self::$cachedCandidates;
    }

    private function lookupXzInPath(): ?string
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'where.exe xz 2>nul' : 'command -v xz 2>/dev/null';
        $result = shell_exec($command);

        if (! filled($result)) {
            return null;
        }

        foreach (preg_split('/\R/', trim($result)) as $line) {
            $candidate = trim($line);

            if ($this->isUsableBinary($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isUsableBinary(string $path): bool
    {
        if (! is_file($path)) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return true;
        }

        return is_executable($path);
    }

    private function convertToBase32(string $data, int $dataLength): string
    {
        $hex = bin2hex("\x00\x00".pack('v', $dataLength).$data);
        $binary = '';

        for ($i = 0; $i < strlen($hex); $i++) {
            $binary .= str_pad(base_convert($hex[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }

        $length = strlen($binary);
        $remainder = $length % 5;

        if ($remainder > 0) {
            $binary .= str_repeat('0', 5 - $remainder);
            $length += 5 - $remainder;
        }

        $base32Chars = '0123456789ABCDEFGHIJKLMNOPQRSTUV';
        $base32 = str_repeat('_', (int) ($length / 5));

        for ($i = 0; $i < $length / 5; $i++) {
            $chunk = substr($binary, $i * 5, 5);
            $value = bindec($chunk);
            $base32[$i] = $base32Chars[$value];
        }

        return $base32;
    }
}
