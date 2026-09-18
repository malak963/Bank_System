<?php

namespace App\Modules\CustomerService\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CustomerService\Services\CustomerServiceService;
use App\Modules\CustomerService\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    private CustomerServiceService $customerServiceService;

    public function __construct(CustomerServiceService $customerServiceService)
    {
        $this->customerServiceService = $customerServiceService;
    }

    public function index(): View
    {
        $tickets = Ticket::with(['customer', 'branch', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statistics = $this->customerServiceService->getTicketStatistics();
        $overdueTickets = $this->customerServiceService->getOverdueTickets();
        $urgentTickets = $this->customerServiceService->getUrgentTickets();

        return view('customerService::index', compact('tickets', 'statistics', 'overdueTickets', 'urgentTickets'));
    }

    public function create(): View
    {
        $categories = \App\Modules\CustomerService\Enums\TicketCategory::cases();
        $priorities = \App\Modules\CustomerService\Enums\TicketPriority::cases();
        $branches = \App\Modules\Branches\Models\Branch::all();
        $customers = \App\Modules\Customers\Models\Customer::all();
        
        return view('customerService::create', compact('categories', 'priorities', 'branches', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'category' => 'required|string',
            'priority' => 'nullable|string',
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        try {
            $ticket = $this->customerServiceService->createTicket($validated);
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Ticket created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ticket creation failed: ' . $e->getMessage());
        }
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['customer', 'branch', 'assignedTo', 'resolvedBy', 'escalatedTo', 'responses.user', 'responses.customer']);
        return view('customerService::show', compact('ticket'));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            $ticket = $this->customerServiceService->assignTicket($ticket, $validated['assigned_to']);
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Ticket assigned successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Assignment failed: ' . $e->getMessage());
        }
    }

    public function resolve(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'resolution' => 'required|string|max:1000',
        ]);

        try {
            $ticket = $this->customerServiceService->resolveTicket($ticket, $validated['resolution']);
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Ticket resolved successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Resolution failed: ' . $e->getMessage());
        }
    }

    public function close(Ticket $ticket)
    {
        try {
            $ticket = $this->customerServiceService->closeTicket($ticket);
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Ticket closed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Close failed: ' . $e->getMessage());
        }
    }

    public function reopen(Ticket $ticket)
    {
        try {
            $ticket = $this->customerServiceService->reopenTicket($ticket);
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Ticket reopened successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Reopen failed: ' . $e->getMessage());
        }
    }

    public function addResponse(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        try {
            $response = $this->customerServiceService->addResponse(
                $ticket,
                $validated['message'],
                $validated['is_internal'] ?? false,
                auth()->id(),
                null
            );
            return redirect()
                ->route('customerService.show', $ticket)
                ->with('success', 'Response added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Response failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = $request->only(['customer_id', 'category', 'priority', 'assigned_to', 'status', 'active_only', 'overdue_only', 'urgent_only']);
        $tickets = $this->customerServiceService->getTickets($filters);
        
        return response()->json($tickets);
    }

    public function apiShow(Ticket $ticket): JsonResponse
    {
        $ticket->load(['customer', 'branch', 'assignedTo', 'resolvedBy', 'escalatedTo', 'responses']);
        return response()->json($ticket);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'category' => 'required|string',
            'priority' => 'nullable|string',
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        try {
            $ticket = $this->customerServiceService->createTicket($validated);
            return response()->json($ticket, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiStatistics(): JsonResponse
    {
        $statistics = $this->customerServiceService->getTicketStatistics();
        return response()->json($statistics);
    }

    public function apiOverdue(): JsonResponse
    {
        $tickets = $this->customerServiceService->getOverdueTickets();
        return response()->json($tickets);
    }

    public function apiUrgent(): JsonResponse
    {
        $tickets = $this->customerServiceService->getUrgentTickets();
        return response()->json($tickets);
    }
}
