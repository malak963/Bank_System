<?php

namespace App\Modules\CashManagement\Services;

use App\Modules\CashManagement\Models\CashOperation;
use Illuminate\Database\Eloquent\Collection;

class CashManagementService
{
    public function getAllOperations(array $filters = []): Collection
    {
        $query = CashOperation::with(['branch', 'teller', 'account', 'approvedBy']);

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['operation_type'])) {
            $query->where('operation_type', $filters['operation_type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getOperation(int $id): CashOperation
    {
        return CashOperation::with(['branch', 'teller', 'account', 'approvedBy'])
            ->findOrFail($id);
    }

    public function createOperation(array $data): CashOperation
    {
        return CashOperation::create([
            'operation_reference' => $this->generateReference(),
            'branch_id' => $data['branch_id'],
            'teller_id' => $data['teller_id'] ?? auth()->id(),
            'account_id' => $data['account_id'] ?? null,
            'operation_type' => $data['operation_type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'SYP',
            'status' => 'pending',
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
            'operation_date' => $data['operation_date'] ?? now(),
            'counterparty_name' => $data['counterparty_name'] ?? null,
            'counterparty_id' => $data['counterparty_id'] ?? null,
        ]);
    }

    public function updateOperation(int $id, array $data): CashOperation
    {
        $operation = $this->getOperation($id);
        $operation->update($data);
        return $operation;
    }

    public function approveOperation(int $id, int $approvedBy): CashOperation
    {
        $operation = $this->getOperation($id);
        $operation->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
        return $operation;
    }

    public function completeOperation(int $id): CashOperation
    {
        $operation = $this->getOperation($id);
        $operation->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        return $operation;
    }

    public function deleteOperation(int $id): bool
    {
        return CashOperation::findOrFail($id)->delete();
    }

    private function generateReference(): string
    {
        return 'CASH-' . strtoupper(uniqid());
    }
}
