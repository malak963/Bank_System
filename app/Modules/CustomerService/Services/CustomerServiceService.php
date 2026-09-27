<?php

namespace App\Modules\CustomerService\Services;

use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use App\Modules\CustomerService\Models\Ticket;
use App\Modules\CustomerService\Models\TicketResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerServiceService
{
    public function getTickets(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Ticket::query()->with(['customer', 'branch', 'assignedTo', 'resolvedBy']);

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createTicket(array $data): Ticket
    {
        if (empty($data['ticket_number'])) {
            $data['ticket_number'] = 'TCK-' . strtoupper(uniqid());
        }
        if (empty($data['status'])) {
            $data['status'] = TicketStatus::Open;
        }
        if (empty($data['priority'])) {
            $data['priority'] = TicketPriority::Medium;
        }

        return Ticket::create($data);
    }

    public function assignTicket(Ticket $ticket, int $userId): Ticket
    {
        $ticket->update([
            'assigned_to' => $userId,
            'status' => TicketStatus::InProgress,
        ]);
        return $ticket;
    }

    public function resolveTicket(Ticket $ticket, string $resolution): Ticket
    {
        $ticket->update([
            'status' => TicketStatus::Resolved,
            'resolution' => $resolution,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
        return $ticket;
    }

    public function closeTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status' => TicketStatus::Closed,
            'closed_at' => now(),
        ]);
        return $ticket;
    }

    public function reopenTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status' => TicketStatus::Open,
            'closed_at' => null,
            'resolved_at' => null,
        ]);
        return $ticket;
    }

    public function addResponse(Ticket $ticket, string $message, bool $isInternal, ?int $userId = null, ?int $customerId = null): TicketResponse
    {
        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);

        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        return $response;
    }

    public function getTicketStatistics(?int $branchId = null): array
    {
        $query = Ticket::query();
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('status', TicketStatus::Open)->count(),
            'in_progress' => (clone $query)->where('status', TicketStatus::InProgress)->count(),
            'resolved' => (clone $query)->where('status', TicketStatus::Resolved)->count(),
            'closed' => (clone $query)->where('status', TicketStatus::Closed)->count(),
            'overdue' => (clone $query)->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
                ->where('created_at', '<', now()->subHours(48))->count(),
            'avg_satisfaction' => round((clone $query)->whereNotNull('customer_satisfaction')->avg('customer_satisfaction') ?? 0, 1),
        ];
    }

    public function getOverdueTickets(?int $branchId = null)
    {
        $query = Ticket::query()->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->where('created_at', '<', now()->subHours(48));

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getUrgentTickets(?int $branchId = null)
    {
        $query = Ticket::query()->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->where('priority', TicketPriority::Urgent);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }
}
