<?php

namespace App\Http\Controllers;

use App\Models\InvoiceEmailLog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class EmailOpenTrackingController extends Controller
{
    public function __invoke(string $token): Response
    {
        $log = InvoiceEmailLog::query()->where('open_token', $token)->first();

        if ($log && $log->opened_at === null) {
            $log->forceFill(['opened_at' => now()])->save();
        }

        $logoPath = public_path('images/email-logo.png');

        if (File::isFile($logoPath)) {
            return response(File::get($logoPath), 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
            ]);
        }

        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
