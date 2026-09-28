<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('email', 'manager@bank.com')->first();
        $managerId = $manager?->id;

        $branches = [
            [
                'code' => 'BR-DAM-01',
                'name' => 'Damascus Main Branch',
                'address' => 'Al-Thawra Street, Central Commercial District, Damascus',
                'phone' => '+963112233441',
                'email' => 'branch.damascus@bank.com',
                'manager_id' => $managerId,
                'status' => BranchStatus::Open,
                'opened_at' => now()->subYears(5),
            ],
            [
                'code' => 'BR-DAM-02',
                'name' => 'Mezzeh Financial Center',
                'address' => 'Mezzeh Autostrad, Financial District, Damascus',
                'phone' => '+963112233442',
                'email' => 'branch.mezzeh@bank.com',
                'manager_id' => $managerId,
                'status' => BranchStatus::Open,
                'opened_at' => now()->subYears(3),
            ],
            [
                'code' => 'BR-ALP-01',
                'name' => 'Aleppo Central Branch',
                'address' => 'Al-Jamiliya Commercial Boulevard, Aleppo',
                'phone' => '+963212233443',
                'email' => 'branch.aleppo@bank.com',
                'manager_id' => null,
                'status' => BranchStatus::Open,
                'opened_at' => now()->subYears(4),
            ],
            [
                'code' => 'BR-HMS-01',
                'name' => 'Homs City Center Branch',
                'address' => 'Al-Dablan Street, Homs',
                'phone' => '+963312233444',
                'email' => 'branch.homs@bank.com',
                'manager_id' => null,
                'status' => BranchStatus::Open,
                'opened_at' => now()->subYears(2),
            ],
            [
                'code' => 'BR-LTK-01',
                'name' => 'Latakia Port Branch',
                'address' => 'Baghdad Street, Coastal District, Latakia',
                'phone' => '+963412233445',
                'email' => 'branch.latakia@bank.com',
                'manager_id' => null,
                'status' => BranchStatus::Open,
                'opened_at' => now()->subYears(2),
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::updateOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );
        }
    }
}
