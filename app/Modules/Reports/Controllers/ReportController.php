<?php

namespace App\Modules\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Branches\Models\Branch;
use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportType;
use App\Modules\Reports\Models\Report;
use App\Modules\Reports\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $reports = Report::with(['branch', 'generatedBy'])->latest()->paginate(15);
        return view('reports::index', compact('reports'));
    }

    public function create(): View
    {
        $reportTypes = ReportType::cases();
        $formats = ReportFormat::cases();
        $branches = Branch::all();

        return view('reports::create', compact('reportTypes', 'formats', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'format' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id',
            'parameters' => 'nullable|array',
        ]);

        $report = $this->reportService->createReport($validated);
        $this->reportService->generateReport($report);

        return redirect()->route('reports.index')->with('success', __('Report created and generating.'));
    }

    public function show(Report $report): View
    {
        return view('reports::show', compact('report'));
    }

    public function download(Report $report)
    {
        if (!$report->file_path || !Storage::exists($report->file_path)) {
            return back()->with('error', __('Report file not found.'));
        }

        return Storage::download($report->file_path, $report->title . '.' . strtolower($report->format->value));
    }

    public function regenerate(Report $report): RedirectResponse
    {
        $this->reportService->regenerateReport($report);
        return back()->with('success', __('Report regeneration initiated.'));
    }

    public function destroy(Report $report): RedirectResponse
    {
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }
        $report->delete();

        return redirect()->route('reports.index')->with('success', __('Report deleted successfully.'));
    }

    public function processScheduled(): JsonResponse
    {
        $count = $this->reportService->processScheduledReports();
        return response()->json(['message' => "Processed {$count} scheduled reports"]);
    }

    public function cleanupExpired(): JsonResponse
    {
        $count = $this->reportService->cleanupExpiredReports();
        return response()->json(['message' => "Cleaned up {$count} expired reports"]);
    }

    // API methods
    public function apiIndex(Request $request): JsonResponse
    {
        return response()->json(Report::latest()->paginate(15));
    }

    public function apiStore(Request $request): JsonResponse
    {
        $report = $this->reportService->createReport($request->all());
        $this->reportService->generateReport($report);
        return response()->json($report, 201);
    }

    public function apiShow(Report $report): JsonResponse
    {
        return response()->json($report);
    }

    public function apiGenerate(Report $report): JsonResponse
    {
        $success = $this->reportService->generateReport($report);
        return response()->json(['success' => $success, 'report' => $report->fresh()]);
    }
}
