<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Branches\Models\Branch;
use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportStatus;
use App\Modules\Reports\Enums\ReportType;
use App\Modules\Reports\Models\Report;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('email', 'manager@bank.com')->first();
        $auditor = User::where('email', 'auditor@bank.com')->first();
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();

        $reports = [
            [
                'report_type' => ReportType::Branch,
                'title' => 'تقرير النشاط المصرفي وحركات الصندوق اليومية - Main Branch EOD Audit',
                'description' => 'ملخص شامل لجميع الإيداعات والسحوبات النقدية وحركات الخزينة لفرع دمشق الرئيسي.',
                'status' => ReportStatus::Completed,
                'format' => ReportFormat::PDF,
                'generated_by' => $manager?->id,
                'branch_id' => $branch->id,
                'file_path' => 'reports/2026/09/branch_dam_daily_20260925.pdf',
                'file_size' => 245760,
                'record_count' => 142,
                'generated_at' => now()->subDays(2),
            ],
            [
                'report_type' => ReportType::Loan,
                'title' => 'تقرير المحفظة الائتمانية ومتابعة الأقساط الشهرية - Loan Portfolio Summary',
                'description' => 'تحليل أداء القروض القائمة ومعدلات السداد ونسب الالتزام بمواعيد الاستحقاق.',
                'status' => ReportStatus::Completed,
                'format' => ReportFormat::Excel,
                'generated_by' => $manager?->id,
                'branch_id' => $branch->id,
                'file_path' => 'reports/2026/09/loan_portfolio_september_2026.xlsx',
                'file_size' => 512000,
                'record_count' => 87,
                'generated_at' => now()->subDays(4),
            ],
            [
                'report_type' => ReportType::Audit,
                'title' => 'تقرير التدقيق الداخلي والامتثال المصرفي - Regulatory Compliance Audit',
                'description' => 'فحص مطابقة إجراءات فتح الحسابات ومكافحة غسل الأموال ومعايير اعرف عميلك KYC.',
                'status' => ReportStatus::Completed,
                'format' => ReportFormat::PDF,
                'generated_by' => $auditor?->id,
                'branch_id' => null,
                'file_path' => 'reports/2026/09/internal_audit_q3_2026.pdf',
                'file_size' => 1048576,
                'record_count' => 320,
                'generated_at' => now()->subDays(7),
            ],
        ];

        foreach ($reports as $r) {
            Report::updateOrCreate(
                ['title' => $r['title']],
                $r
            );
        }
    }
}
