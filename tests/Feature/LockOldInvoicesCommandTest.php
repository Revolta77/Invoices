<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LockOldInvoicesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_locks_only_invoices_older_than_two_months(): void
    {
        $this->travelTo(now()->setDate(2026, 8, 1)->startOfDay());

        $user = User::factory()->create();
        $profile = CompanyProfile::query()->create([
            'user_id' => $user->id,
            'name' => 'Test company',
        ]);

        $oldInvoice = Invoice::query()->create([
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'number' => '2026-001',
            'partner_name' => 'Old partner',
            'issue_date' => '2026-05-31',
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid',
            'total' => 100,
        ]);

        $borderlineInvoice = Invoice::query()->create([
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'number' => '2026-002',
            'partner_name' => 'Borderline partner',
            'issue_date' => '2026-06-01',
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid',
            'total' => 100,
        ]);

        $recentInvoice = Invoice::query()->create([
            'company_profile_id' => $profile->id,
            'user_id' => $user->id,
            'number' => '2026-003',
            'partner_name' => 'Recent partner',
            'issue_date' => '2026-07-15',
            'payment_method' => 'bank_transfer',
            'status' => 'unpaid',
            'total' => 100,
        ]);

        $this->artisan('invoices:lock-old')
            ->expectsOutput('Locked 1 invoice(s) issued before 2026-06-01.')
            ->assertSuccessful();

        $this->assertTrue($oldInvoice->fresh()->is_locked);
        $this->assertNotNull($oldInvoice->fresh()->locked_at);
        $this->assertFalse($borderlineInvoice->fresh()->is_locked);
        $this->assertFalse($recentInvoice->fresh()->is_locked);
    }
}
