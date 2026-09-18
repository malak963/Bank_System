<?php

namespace App\Modules\Reports\Services;

use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportStatus;
use App\Modules\Reports\Enums\ReportType;
use App\Modules\Reports\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function createReport(array $data): Report
    {
        return Report::create([
            'report_type' => $data['report_type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => ReportStatus::Pending,
            'format' => $data['format'] ?? ReportFormat::PDF,
            'parameters' => $data['parameters'] ?? null,
            'generated_by' => auth()->id(),
            'branch_id' => $data['branch_id'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    public function generateReport(Report $report): bool
    {
        try {
            $report->update([
                'status' => ReportStatus::Generating,
            ]);

            $data = $this->generateReportData($report);
            $filePath = $this->generateReportFile($report, $data);

            $report->update([
                'status' => ReportStatus::Completed,
                'file_path' => $filePath,
                'file_size' => Storage::size($filePath),
                'record_count' => count($data),
                'generated_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            Log::info("Report generated successfully", [
                'report_id' => $report->id,
                'report_type' => $report->report_type->value,
                'record_count' => count($data),
            ]);

            return true;

        } catch (\Exception $e) {
            $report->update([
                'status' => ReportStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Report generation failed", [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function generateTransactionReport(array $parameters): array
    {
        $query = \App\Modules\Transactions\Models\Transaction::query();

        if (isset($parameters['start_date'])) {
            $query->where('created_at', '>=', $parameters['start_date']);
        }

        if (isset($parameters['end_date'])) {
            $query->where('created_at', '<=', $parameters['end_date']);
        }

        if (isset($parameters['branch_id'])) {
            $query->where('branch_id', $parameters['branch_id']);
        }

        if (isset($parameters['customer_id'])) {
            $query->where('customer_id', $parameters['customer_id']);
        }

        if (isset($parameters['transaction_type'])) {
            $query->where('transaction_type', $parameters['transaction_type']);
        }

        $transactions = $query->with(['account', 'customer', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'summary' => [
                'total_transactions' => $transactions->count(),
                'total_amount' => $transactions->sum('amount'),
                'total_credits' => $transactions->where('transaction_type', 'deposit')->sum('amount'),
                'total_debits' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
                'successful_transactions' => $transactions->where('status', 'completed')->count(),
                'failed_transactions' => $transactions->where('status', 'failed')->count(),
            ],
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'reference' => $transaction->transaction_reference,
                    'type' => $transaction->transaction_type->value,
                    'amount' => $transaction->amount,
                    'account' => $transaction->account?->account_number,
                    'customer' => $transaction->customer?->full_name,
                    'branch' => $transaction->branch?->name,
                    'status' => $transaction->status->value,
                    'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),
        ];
    }

    public function generateAccountReport(array $parameters): array
    {
        $query = \App\Modules\Accounts\Models\Account::query();

        if (isset($parameters['branch_id'])) {
            $query->where('branch_id', $parameters['branch_id']);
        }

        if (isset($parameters['customer_id'])) {
            $query->where('customer_id', $parameters['customer_id']);
        }

        if (isset($parameters['status'])) {
            $query->where('status', $parameters['status']);
        }

        $accounts = $query->with(['customer', 'branch', 'accountType'])
            ->get();

        return [
            'summary' => [
                'total_accounts' => $accounts->count(),
                'total_balance' => $accounts->sum('balance'),
                'active_accounts' => $accounts->where('status', 'open')->count(),
                'frozen_accounts' => $accounts->where('status', 'frozen')->count(),
                'closed_accounts' => $accounts->where('status', 'closed')->count(),
            ],
            'accounts' => $accounts->map(function ($account) {
                return [
                    'account_number' => $account->account_number,
                    'customer' => $account->customer?->full_name,
                    'branch' => $account->branch?->name,
                    'account_type' => $account->accountType?->name,
                    'balance' => $account->balance,
                    'status' => $account->status->value,
                    'opened_at' => $account->opened_at?->format('Y-m-d'),
                ];
            })->toArray(),
        ];
    }

    public function generateRevenueReport(array $parameters): array
    {
        $startDate = $parameters['start_date'] ?? now()->startOfMonth();
        $endDate = $parameters['end_date'] ?? now();

        $transactions = \App\Modules\Transactions\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $fees = $transactions->sum('fees');
        $interest = \App\Modules\Loans\Models\Loan::whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_interest');

        return [
            'summary' => [
                'period_start' => $startDate,
                'period_end' => $endDate,
                'total_fees' => $fees,
                'total_interest' => $interest,
                'total_revenue' => $fees + $interest,
                'transaction_count' => $transactions->count(),
            ],
            'breakdown' => [
                'fees_by_type' => $transactions->groupBy('transaction_type')->map(function ($group) {
                    return $group->sum('fees');
                })->toArray(),
                'interest_by_loan_type' => \App\Modules\Loans\Models\Loan::whereBetween('created_at', [$startDate, $endDate])
                    ->with('loanType')
                    ->get()
                    ->groupBy('loan_type_id')
                    ->map(function ($group) {
                        return $group->sum('total_interest');
                    })->toArray(),
            ],
        ];
    }

    public function generateCustomerReport(array $parameters): array
    {
        $query = \App\Modules\Customers\Models\Customer::query();

        if (isset($parameters['branch_id'])) {
            $query->where('branch_id', $parameters['branch_id']);
        }

        if (isset($parameters['status'])) {
            $query->where('status', $parameters['status']);
        }

        if (isset($parameters['kyc_status'])) {
            $query->where('kyc_status', $parameters['kyc_status']);
        }

        $customers = $query->with(['accounts', 'branch'])
            ->get();

        return [
            'summary' => [
                'total_customers' => $customers->count(),
                'active_customers' => $customers->where('status', 'active')->count(),
                'kyc_verified' => $customers->where('kyc_status', 'verified')->count(),
                'kyc_pending' => $customers->where('kyc_status', 'pending')->count(),
                'total_accounts' => $customers->sum(fn ($c) => $c->accounts->count()),
            ],
            'customers' => $customers->map(function ($customer) {
                return [
                    'customer_number' => $customer->customer_number,
                    'name' => $customer->full_name,
                    'branch' => $customer->branch?->name,
                    'status' => $customer->status->value,
                    'kyc_status' => $customer->kyc_status->value,
                    'account_count' => $customer->accounts->count(),
                    'total_balance' => $customer->accounts->sum('balance'),
                    'created_at' => $customer->created_at->format('Y-m-d'),
                ];
            })->toArray(),
        ];
    }

    public function regenerateReport(Report $report): bool
    {
        if (!$report->canBeRegenerated()) {
            throw new \Exception('This report cannot be regenerated');
        }

        // Delete old file if exists
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        // Reset report status
        $report->update([
            'status' => ReportStatus::Pending,
            'file_path' => null,
            'file_size' => null,
            'record_count' => null,
            'generated_at' => null,
            'error_message' => null,
        ]);

        return $this->generateReport($report);
    }

    public function processScheduledReports(): int
    {
        $scheduledReports = Report::scheduled()
            ->where('scheduled_at', '<=', now())
            ->get();

        $processedCount = 0;

        foreach ($scheduledReports as $report) {
            if ($this->generateReport($report)) {
                $processedCount++;
            }
        }

        return $processedCount;
    }

    public function cleanupExpiredReports(): int
    {
        $expiredReports = Report::completed()
            ->expiringSoon(0)
            ->get();

        $deletedCount = 0;

        foreach ($expiredReports as $report) {
            if ($report->file_path && Storage::exists($report->file_path)) {
                Storage::delete($report->file_path);
            }
            $report->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    public function downloadReport(Report $report): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$report->canBeDownloaded()) {
            throw new \Exception('Report cannot be downloaded');
        }

        return Storage::download($report->file_path, $report->title . '.' . $report->format->fileExtension());
    }

    private function generateReportData(Report $report): array
    {
        return match ($report->report_type) {
            ReportType::Transaction => $this->generateTransactionReport($report->parameters ?? []),
            ReportType::Account => $this->generateAccountReport($report->parameters ?? []),
            ReportType::Revenue => $this->generateRevenueReport($report->parameters ?? []),
            ReportType::Customer => $this->generateCustomerReport($report->parameters ?? []),
            default => throw new \Exception('Report type not implemented'),
        };
    }

    private function generateReportFile(Report $report, array $data): string
    {
        $fileName = $report->title . '_' . $report->id . '_' . time();
        $extension = $report->format->fileExtension();
        $filePath = 'reports/' . $fileName . '.' . $extension;

        switch ($report->format) {
            case ReportFormat::JSON:
                Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
                break;
            case ReportFormat::CSV:
                Storage::put($filePath, $this->convertToCSV($data));
                break;
            case ReportFormat::PDF:
                // Placeholder for PDF generation
                // In production, integrate with PDF library like DomPDF or TCPDF
                Storage::put($filePath, json_encode($data)); // Temporarily use JSON
                break;
            case ReportFormat::Excel:
                // Placeholder for Excel generation
                // In production, integrate with Excel library like PhpSpreadsheet
                Storage::put($filePath, json_encode($data)); // Temporarily use JSON
                break;
        }

        return $filePath;
    }

    private function convertToCSV(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($output, $row);
            }
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}
