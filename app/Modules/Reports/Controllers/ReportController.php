<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Reports\Services\ReportService;
use App\Modules\Reports\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(): View
    {
        $reports = Report::with(['branch', 'generatedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reports::index', compact('reports'));
    }

    public function create(): View
    {
        $reportTypes = \App\Modules\Reports\Enums\ReportType::cases();
        $formats = \App\Modules\Reports\Enums\ReportFormat::cases();
        $branches = \App\Modules\Branches\Models\Branch::all();
        
        return view('reports::create', compact('reportTypes', 'formats', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'format' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'customer_id' => 'nullable|exists:customers,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $parameters = [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
        ];

        try {
            $report = $this->reportService->createReport([
                'report_type' => $validated['report_type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'format' => $validated['format'],
                'parameters' => $parameters,
                'branch_id' => $validated['branch_id'] ?? null,
                'scheduled_at' => $validated['scheduled_at'] ?? null,
            ]);

            // Generate immediately if not scheduled
            if (!$validated['scheduled_at']) {
                $this->reportService->generateReport($report);
            }

            return redirect()
                ->route('reports.show', $report)
                ->with('success', 'Report created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Report creation failed: ' . $e->getMessage());
        }
    }

    public function show(Report $report): View
    {
        $report->load(['branch', 'generatedBy']);
        return view('reports::show', compact('report'));
    }

    public function download(Report $report)
    {
        try {
            return $this->reportService->downloadReport($report);
        } catch (\Exception $e) {
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    public function regenerate(Report $report)
    {
        try {
            $this->reportService->regenerateReport($report);
            return redirect()
                ->route('reports.show', $report)
                ->with('success', 'Report regenerated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Regeneration failed: ' . $e->getMessage());
        }
    }

    public function processScheduled()
    {
        try {
            $count = $this->reportService->processScheduledReports();
            return back()->with('success', "{$count} scheduled reports processed");
        } catch (\Exception $e) {
            return back()->with('error', 'Processing failed: ' . $e->getMessage());
        }
    }

    public function cleanupExpired()
    {
        try {
            $count = $this->reportService->cleanupExpiredReports();
            return back()->with('success', "{$count} expired reports cleaned up");
        } catch (\Exception $e) {
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }

    // API Methods
    public function apiIndex(Request $request): JsonResponse
    {
        $query = Report::with(['branch', 'generatedBy']);

        if ($request->has('type')) {
            $query->where('report_type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($reports);
    }

    public function apiShow(Report $report): JsonResponse
    {
        $report->load(['branch', 'generatedBy']);
        return response()->json($report);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'format' => 'required|string',
            'parameters' => 'nullable|array',
            'branch_id' => 'nullable|exists:branches,id',
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            $report = $this->reportService->createReport($validated);
            return response()->json($report, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function apiGenerate(Report $report): JsonResponse
    {
        try {
            $success = $this->reportService->generateReport($report);
            return response()->json(['success' => $success]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
