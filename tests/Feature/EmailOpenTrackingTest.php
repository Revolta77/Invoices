<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\InvoiceEmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class EmailOpenTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_email_as_opened_and_returns_logo(): void
    {
        $user = User::factory()->create();
        $profile = CompanyProfile::query()->create([
            'user_id' => $user->id,
            'name' => 'Test company',
        ]);

        $invoice = Invoice::query()->create([
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'number' => '2026-001',
            'partner_name' => 'Partner',
            'issue_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid',
            'total' => 100,
        ]);

        $log = InvoiceEmailLog::query()->create([
            'invoice_id' => $invoice->id,
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'partner_name' => 'Partner',
            'to_email' => 'partner@example.com',
            'from_email' => 'me@example.com',
            'subject' => 'Invoice',
            'sent_at' => now(),
            'open_token' => 'test-open-token',
        ]);

        File::ensureDirectoryExists(public_path('images'));
        File::put(public_path('images/email-logo.png'), base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        ));

        $response = $this->get(route('email.open', ['token' => 'test-open-token']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertNotNull($log->fresh()->opened_at);

        File::delete(public_path('images/email-logo.png'));
    }

    public function test_it_does_not_overwrite_existing_open_timestamp(): void
    {
        $user = User::factory()->create();
        $profile = CompanyProfile::query()->create([
            'user_id' => $user->id,
            'name' => 'Test company',
        ]);

        $invoice = Invoice::query()->create([
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'number' => '2026-002',
            'partner_name' => 'Partner',
            'issue_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid',
            'total' => 100,
        ]);

        $openedAt = now()->subHour();

        $log = InvoiceEmailLog::query()->create([
            'invoice_id' => $invoice->id,
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'partner_name' => 'Partner',
            'to_email' => 'partner@example.com',
            'from_email' => 'me@example.com',
            'subject' => 'Invoice',
            'sent_at' => now()->subHours(2),
            'open_token' => 'already-open-token',
            'opened_at' => $openedAt,
        ]);

        $this->get(route('email.open', ['token' => 'already-open-token']))->assertOk();

        $this->assertSame(
            $openedAt->format('Y-m-d H:i:s'),
            $log->fresh()->opened_at->format('Y-m-d H:i:s'),
        );
    }
}
