<?php

namespace App\Modules\CustomerService\Services;

use App\Modules\CustomerService\Enums\TicketCategory;
use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use App\Modules\CustomerService\Models\Ticket;
use App\Modules\CustomerService\Models\TicketResponse;
use Illuminate\Support\Facades\Log;

class CustomerServiceService
{
    public function createTicket(array $data): Ticket
    {
        $category = TicketCategory::from($data['category']);
        $priority = $data['priority'] ?? $category->defaultPriority();

        $ticket = Ticket::create([
            'ticket_number' => $this->generateTicketNumber(),
            'customer_id' => $data['customer_id'] ?? null,
            'branch_id' => $data['branch_id'] ?? null,
            'category' => $category,
            'priority' => $priority,
            'status' => TicketStatus::Open,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'tags' => $data['tags'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);

        // Auto-escalate if required
        if ($category->requiresEscalation()) {
            $this->autoEscalateTicket($ticket);
        }

        Log::info("Customer service ticket created", [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'category' => $category->value,
            'priority' => $priority->value,
        ]);

        return $ticket;
    }

    public function addResponse(Ticket $ticket, string $message, bool $isInternal = false, ?int $userId = null, ?int $customerId = null): TicketResponse
    {
        $response = TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'customer_id' => $customerId,
            'message' => $message,
            'is_internal' => $isInternal,
            'attachments' => [],
            'metadata' => [],
        ]);

        // Update ticket first response time if this is the first public response
        if (!$isInternal && !$ticket->first_response_at) {
            $ticket->recordFirstResponse();
        }

        // Update ticket status based on response
        if ($isInternal) {
            $ticket->update(['status' => TicketStatus::InProgress]);
        } else {
            $ticket->update(['status' => TicketStatus::PendingCustomer]);
        }

        Log::info("Ticket response added", [
            'ticket_id' => $ticket->id,
            'response_id' => $response->id,
            'is_internal' => $isInternal,
        ]);

        return $response;
    }

    public function assignTicket(Ticket $ticket, int $userId): Ticket
    {
        $ticket->assign($userId);

        Log::info("Ticket assigned", [
            'ticket_id' => $ticket->id,
            'assigned_to' => $userId,
        ]);

        return $ticket->fresh();
    }

    public function resolveTicket(Ticket $ticket, string $resolution, ?int $resolvedBy = null): Ticket
    {
        $ticket->resolve($resolution, $resolvedBy);

        Log::info("Ticket resolved", [
            'ticket_id' => $ticket->id,
            'resolved_by' => $resolvedBy,
        ]);

        return $ticket->fresh();
    }

    public function closeTicket(Ticket $ticket, ?int $closedBy = null): Ticket
    {
        $ticket->close($closedBy);

        Log::info("Ticket closed", [
            'ticket_id' => $ticket->id,
            'closed_by' => $closedBy,
        ]);

        return $ticket->fresh();
    }

    public function reopenTicket(Ticket $ticket): Ticket
    {
        $ticket->reopen();

        Log::info("Ticket reopened", [
            'ticket_id' => $ticket->id,
        ]);

        return $ticket->fresh();
    }

    public function escalateTicket(Ticket $ticket, int $escalatedTo, string $reason): Ticket
    {
        $ticket->escalate($escalatedTo, $reason);

        Log::info("Ticket escalated", [
            'ticket_id' => $ticket->id,
            'escalated_to' => $escalatedTo,
            'reason' => $reason,
        ]);

        return $ticket->fresh();
    }

    public function updateCustomerSatisfaction(Ticket $ticket, int $rating): Ticket
    {
        $ticket->update(['customer_satisfaction' => $rating]);

        Log::info("Customer satisfaction updated", [
            'ticket_id' => $ticket->id,
            'rating' => $rating,
        ]);

        return $ticket->fresh();
    }

    public function getTickets(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Ticket::query();

        if (isset($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (isset($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (isset($filters['priority'])) {
            $query->byPriority($filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $query->byAssignee($filters['assigned_to']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $query->active();
        }

        if (isset($filters['overdue_only']) && $filters['overdue_only']) {
            $query->overdue();
        }

        if (isset($filters['urgent_only']) && $filters['urgent_only']) {
            $query->urgent();
        }

        return $query->with(['customer', 'branch', 'assignedTo', 'responses'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTicketStatistics(): array
    {
        $total = Ticket::count();
        $open = Ticket::open()->count();
        $inProgress = Ticket::where('status', TicketStatus::InProgress)->count();
        $resolved = Ticket::where('status', TicketStatus::Resolved)->count();
        $closed = Ticket::where('status', TicketStatus::Closed)->count();
        $overdue = Ticket::overdue()->count();
        $urgent = Ticket::urgent()->count();

        $avgSatisfaction = Ticket::whereNotNull('customer_satisfaction')
            ->avg('customer_satisfaction');

        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total' => $total,
            'open' => $open,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'closed' => $closed,
            'overdue' => $overdue,
            'urgent' => $urgent,
            'avg_satisfaction' => round($avgSatisfaction, 2),
            'avg_response_time_hours' => round($avgResponseTime, 2),
        ];
    }

    public function getTicketsByCategory(): array
    {
        return Ticket::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => $item->count];
            })
            ->toArray();
    }

    public function getOverdueTickets(): \Illuminate\Database\Eloquent\Collection
    {
        return Ticket::overdue()
            ->with(['customer', 'assignedTo'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getUrgentTickets(): \Illuminate\Database\Eloquent\Collection
    {
        return Ticket::urgent()
            ->active()
            ->with(['customer', 'assignedTo'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function generateTicketNumber(): string
    {
        return 'TKT' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function autoEscalateTicket(Ticket $ticket): void
    {
        // Find a manager or supervisor to escalate to
        $manager = \App\Models\User::where('role', 'admin')
            ->orWhere('role', 'manager')
            ->first();

        if ($manager) {
            $ticket->escalate($manager->id, 'Auto-escalated due to category requirements');
        }
    }
}
