<?php

namespace App\Modules\Security\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Security\Services\SecurityService;
use App\Modules\Security\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SecurityController extends Controller
{
    private SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    public function index(): View
    {
        $events = SecurityEvent::with(['user', 'customer', 'resolver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $criticalEvents = $this->securityService->getCriticalEvents();
        $fraudAlerts = $this->securityService->getFraudAlerts();

        return view('security::index', compact('events', 'criticalEvents', 'fraudAlerts'));
    }

    public function show(SecurityEvent $event): View
    {
        $event->load(['user', 'customer', 'resolver', 'relatedEntity']);
        return view('security::show', compact('event'));
    }

    public function resolve(Request $request, SecurityEvent $event)
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string|max:500',
        ]);

        try {
            $event = $this->securityService->resolveSecurityEvent($event, $validated['resolution_notes']);
            return redirect()
                ->route('security.show', $event)
                ->with('success', 'Security event resolved successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Resolution failed: ' . $e->getMessage());
        }
    }

    public function block(Request $request, SecurityEvent $event)
    {
        $validated = $request->validate([
            'block_duration_hours' => 'nullable|integer|min:1|max:720',
        ]);

        try {
            $until = $validated['block_duration_hours'] 
                ? now()->addHours($validated['block_duration_hours']) 
                : null;
            
            $event->block($until);
            return redirect()
                ->route('security.show', $event)
                ->with('success', 'User blocked successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Block failed: ' . $e->getMessage());
        }
    }

    public function unblock(SecurityEvent $event)
    {
        try {
            $event->unblock();
            return redirect()
                ->route('security.show', $event)
                ->with('success', 'User unblocked successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Unblock failed: ' . $e->getMessage());
        }
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        try {
            $anomalies = $this->securityService->detectAnomalousPattern($validated['customer_id']);
            return view('security::analysis', compact('anomalies', 'customerId'));
        } catch (\Exception $e) {
            return back()->with('error', 'Analysis failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = $request->only(['user_id', 'customer_id', 'event_type', 'security_level', 'unresolved_only', 'blocked_only', 'hours']);
        $events = $this->securityService->getSecurityEvents($filters);
        
        return response()->json($events);
    }

    public function apiShow(SecurityEvent $event): JsonResponse
    {
        $event->load(['user', 'customer', 'resolver', 'relatedEntity']);
        return response()->json($event);
    }

    public function apiResolve(Request $request, SecurityEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'resolution_notes' => 'required|string|max:500',
        ]);

        try {
            $event = $this->securityService->resolveSecurityEvent($event, $validated['resolution_notes']);
            return response()->json($event);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiLog(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'description' => 'nullable|string',
            'details' => 'nullable|array',
            'security_level' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            $event = $this->securityService->logSecurityEvent($validated);
            return response()->json($event, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiCritical(): JsonResponse
    {
        $events = $this->securityService->getCriticalEvents();
        return response()->json($events);
    }

    public function apiFraudAlerts(): JsonResponse
    {
        $events = $this->securityService->getFraudAlerts();
        return response()->json($events);
    }

    public function apiAnalyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        try {
            $anomalies = $this->securityService->detectAnomalousPattern($validated['customer_id']);
            return response()->json(['anomalies' => $anomalies]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
