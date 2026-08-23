<?php

namespace Tests\Feature\Modules\Customers;

use App\Models\User;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_routes_require_authentication(): void
    {
        $this->get('/customers')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_customers_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Customers');
    }

    public function test_authenticated_user_can_create_customer(): void
    {
        $actor = User::factory()->create();
        $linkedUser = User::factory()->create();

        $response = $this->actingAs($actor)
            ->post(route('customers.store'), [
                'user_id' => $linkedUser->id,
                'customer_number' => 'cust-100001',
                'first_name' => 'Maya',
                'last_name' => 'Haddad',
                'date_of_birth' => '1990-04-20',
                'national_id' => 'nat-123456',
                'identity_document_type' => IdentityDocumentType::Passport->value,
                'identity_document_number' => 'pass-987654',
                'identity_document_country' => 'sy',
                'identity_document_expires_at' => now()->addYears(4)->toDateString(),
                'phone_number' => '+963 944 123 456',
                'address' => 'Damascus',
                'status' => CustomerStatus::Active->value,
                'kyc_status' => KycStatus::Approved->value,
                'kyc_reference' => 'kyc-2026-0001',
                'risk_level' => RiskLevel::Low->value,
            ]);

        $customer = Customer::query()->firstOrFail();

        $response->assertRedirect(route('customers.show', $customer));

        $this->assertDatabaseHas('customers', [
            'user_id' => $linkedUser->id,
            'customer_number' => 'CUST-100001',
            'national_id' => 'NAT-123456',
            'identity_document_type' => IdentityDocumentType::Passport->value,
            'identity_document_number' => 'PASS-987654',
            'identity_document_country' => 'SY',
            'status' => CustomerStatus::Active->value,
            'kyc_status' => KycStatus::Approved->value,
            'kyc_reference' => 'KYC-2026-0001',
            'kyc_reviewed_by' => $actor->id,
            'risk_level' => RiskLevel::Low->value,
        ]);
    }

    public function test_customer_index_supports_kyc_search_filtering_and_sorting(): void
    {
        $actor = User::factory()->create();

        Customer::factory()
            ->for(User::factory()->create(['email' => 'lina.compliance@example.test']))
            ->create([
                'customer_number' => 'CUST-200001',
                'first_name' => 'Lina',
                'last_name' => 'Karam',
                'national_id' => 'NAT-FILTER-001',
                'identity_document_type' => IdentityDocumentType::Passport,
                'identity_document_number' => 'PASS-FILTER-001',
                'identity_document_country' => 'SY',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-SY-APPROVED',
                'kyc_reviewed_by' => $actor->id,
                'kyc_reviewed_at' => '2026-08-10 09:00:00',
                'risk_level' => RiskLevel::High,
            ]);

        Customer::factory()
            ->for(User::factory()->create(['email' => 'nour.pending@example.test']))
            ->create([
                'customer_number' => 'CUST-200002',
                'first_name' => 'Nour',
                'last_name' => 'Salem',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_country' => 'LB',
                'kyc_status' => KycStatus::Pending,
                'risk_level' => RiskLevel::Low,
            ]);

        $this->actingAs($actor)
            ->get(route('customers.index', [
                'search' => 'kyc-sy',
                'kyc_status' => KycStatus::Approved->value,
                'risk_level' => RiskLevel::High->value,
                'identity_document_type' => IdentityDocumentType::Passport->value,
                'identity_document_country' => 'sy',
                'verified_from' => '2026-08-01',
                'verified_to' => '2026-08-20',
                'sort' => 'risk',
            ]))
            ->assertOk()
            ->assertSee('Lina')
            ->assertSee('KYC-SY-APPROVED')
            ->assertDontSee('Nour');
    }

    public function test_customer_validation_rejects_duplicate_identity_and_invalid_enums(): void
    {
        $actor = User::factory()->create();
        $linkedUser = User::factory()->create();

        Customer::factory()
            ->for($linkedUser)
            ->create([
                'customer_number' => 'CUST-100001',
                'national_id' => 'NAT-123456',
            ]);

        $this->actingAs($actor)
            ->from(route('customers.create'))
            ->post(route('customers.store'), [
                'user_id' => $linkedUser->id,
                'customer_number' => 'CUST-100001',
                'first_name' => 'Maya',
                'last_name' => 'Haddad',
                'date_of_birth' => now()->addDay()->toDateString(),
                'national_id' => 'NAT-123456',
                'identity_document_type' => IdentityDocumentType::Passport->value,
                'status' => 'unknown',
                'kyc_status' => KycStatus::Rejected->value,
                'risk_level' => 'unknown',
            ])
            ->assertRedirect(route('customers.create'))
            ->assertSessionHasErrors([
                'user_id',
                'customer_number',
                'date_of_birth',
                'national_id',
                'identity_document_number',
                'status',
                'kyc_rejection_reason',
                'risk_level',
            ]);
    }
}
