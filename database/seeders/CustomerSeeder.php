<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use App\Modules\Customers\Models\Customer;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $damascusMain = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $mezzehBranch = Branch::where('code', 'BR-DAM-02')->first() ?? $damascusMain;
        $aleppoBranch = Branch::where('code', 'BR-ALP-01')->first() ?? $damascusMain;
        $homsBranch = Branch::where('code', 'BR-HMS-01')->first() ?? $damascusMain;
        $latakiaBranch = Branch::where('code', 'BR-LTK-01')->first() ?? $damascusMain;

        $managerUser = User::where('email', 'manager@bank.com')->first();
        $managerId = $managerUser?->id;

        $customersData = [
            [
                'email' => 'customer@bank.com',
                'name' => 'Ahmad Al-Sayed',
                'phone' => '+963944444444',
                'customer_number' => 'CUST-000001',
                'first_name' => 'Ahmad',
                'last_name' => 'Al-Sayed',
                'date_of_birth' => '1988-04-15',
                'national_id' => 'NAT0102008801',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-DAM-992101',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(5)->format('Y-m-d'),
                'address' => 'Shaalan District, Damascus',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-001',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonths(6),
                'risk_level' => RiskLevel::Low,
                'branch_id' => $damascusMain?->id,
            ],
            [
                'email' => 'layla.mansour@bank.com',
                'name' => 'Layla Mansour',
                'phone' => '+963944111222',
                'customer_number' => 'CUST-000002',
                'first_name' => 'Layla',
                'last_name' => 'Mansour',
                'date_of_birth' => '1992-09-20',
                'national_id' => 'NAT0102009202',
                'identity_document_type' => IdentityDocumentType::Passport,
                'identity_document_number' => 'PASS-SY-459812',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(3)->format('Y-m-d'),
                'address' => 'Abu Roummaneh, Damascus',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-002',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonths(5),
                'risk_level' => RiskLevel::Low,
                'branch_id' => $mezzehBranch?->id,
            ],
            [
                'email' => 'omar.khaled@bank.com',
                'name' => 'Omar Khaled',
                'phone' => '+963944333444',
                'customer_number' => 'CUST-000003',
                'first_name' => 'Omar',
                'last_name' => 'Khaled',
                'date_of_birth' => '1982-11-05',
                'national_id' => 'NAT0102008203',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-DAM-773142',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(4)->format('Y-m-d'),
                'address' => 'Malki Gardens, Damascus',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-003',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonths(4),
                'risk_level' => RiskLevel::Medium,
                'branch_id' => $damascusMain?->id,
            ],
            [
                'email' => 'nour.hassan@bank.com',
                'name' => 'Nour Al-Hassan',
                'phone' => '+963944555666',
                'customer_number' => 'CUST-000004',
                'first_name' => 'Nour',
                'last_name' => 'Al-Hassan',
                'date_of_birth' => '1995-02-18',
                'national_id' => 'NAT0201009504',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-ALP-109283',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(6)->format('Y-m-d'),
                'address' => 'Al-Shahbaa District, Aleppo',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-004',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonths(3),
                'risk_level' => RiskLevel::Low,
                'branch_id' => $aleppoBranch?->id,
            ],
            [
                'email' => 'tariq.halabi@bank.com',
                'name' => 'Tariq Al-Halabi',
                'phone' => '+963944777888',
                'customer_number' => 'CUST-000005',
                'first_name' => 'Tariq',
                'last_name' => 'Al-Halabi',
                'date_of_birth' => '1979-06-30',
                'national_id' => 'NAT0201007905',
                'identity_document_type' => IdentityDocumentType::Passport,
                'identity_document_number' => 'PASS-SY-981245',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(2)->format('Y-m-d'),
                'address' => 'Al-Mogambo, Aleppo',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-005',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonths(2),
                'risk_level' => RiskLevel::Medium,
                'branch_id' => $aleppoBranch?->id,
            ],
            [
                'email' => 'maya.haddad@bank.com',
                'name' => 'Maya Haddad',
                'phone' => '+963944999000',
                'customer_number' => 'CUST-000006',
                'first_name' => 'Maya',
                'last_name' => 'Haddad',
                'date_of_birth' => '1996-12-04',
                'national_id' => 'NAT0401009606',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-LTK-554433',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(7)->format('Y-m-d'),
                'address' => 'Corniche Al-Gharbi, Latakia',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Approved,
                'kyc_reference' => 'KYC-2024-006',
                'kyc_reviewed_by' => $managerId,
                'kyc_reviewed_at' => now()->subMonth(),
                'risk_level' => RiskLevel::Low,
                'branch_id' => $latakiaBranch?->id,
            ],
            [
                'email' => 'ziad.kassam@bank.com',
                'name' => 'Ziad Kassam',
                'phone' => '+963944123987',
                'customer_number' => 'CUST-000007',
                'first_name' => 'Ziad',
                'last_name' => 'Kassam',
                'date_of_birth' => '1990-08-14',
                'national_id' => 'NAT0301009007',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-HMS-667788',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(5)->format('Y-m-d'),
                'address' => 'Al-Inshaat, Homs',
                'status' => CustomerStatus::Active,
                'kyc_status' => KycStatus::Pending,
                'kyc_reference' => null,
                'kyc_reviewed_by' => null,
                'kyc_reviewed_at' => null,
                'risk_level' => RiskLevel::Medium,
                'branch_id' => $homsBranch?->id,
            ],
            [
                'email' => 'karim.nasser@bank.com',
                'name' => 'Karim Nasser',
                'phone' => '+963944321789',
                'customer_number' => 'CUST-000008',
                'first_name' => 'Karim',
                'last_name' => 'Nasser',
                'date_of_birth' => '2001-03-25',
                'national_id' => 'NAT0102000108',
                'identity_document_type' => IdentityDocumentType::NationalId,
                'identity_document_number' => 'ID-DAM-112299',
                'identity_document_country' => 'SY',
                'identity_document_expires_at' => now()->addYears(8)->format('Y-m-d'),
                'address' => 'Kafar Souseh, Damascus',
                'status' => CustomerStatus::Prospect,
                'kyc_status' => KycStatus::Pending,
                'kyc_reference' => null,
                'kyc_reviewed_by' => null,
                'kyc_reviewed_at' => null,
                'risk_level' => RiskLevel::Low,
                'branch_id' => $damascusMain?->id,
            ],
        ];

        foreach ($customersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'role' => UserRole::Customer,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'branch_id' => $data['branch_id'],
                    'customer_number' => $data['customer_number'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'national_id' => $data['national_id'],
                    'identity_document_type' => $data['identity_document_type'],
                    'identity_document_number' => $data['identity_document_number'],
                    'identity_document_country' => $data['identity_document_country'],
                    'identity_document_expires_at' => $data['identity_document_expires_at'],
                    'phone_number' => $data['phone'],
                    'address' => $data['address'],
                    'status' => $data['status'],
                    'kyc_status' => $data['kyc_status'],
                    'kyc_reference' => $data['kyc_reference'],
                    'kyc_reviewed_by' => $data['kyc_reviewed_by'],
                    'kyc_reviewed_at' => $data['kyc_reviewed_at'],
                    'risk_level' => $data['risk_level'],
                ]
            );
        }
    }
}
