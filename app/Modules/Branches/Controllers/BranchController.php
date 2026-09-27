<?php

namespace App\Modules\Branches\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use App\Modules\Branches\Requests\IndexBranchRequest;
use App\Modules\Branches\Requests\StoreBranchRequest;
use App\Modules\Branches\Requests\UpdateBranchRequest;
use App\Modules\Branches\Services\BranchService;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(private BranchService $service) {}

    public function index(IndexBranchRequest $request): View
    {
        $filters = $request->filters();

        return view('branches::index', [
            'branches' => $this->service->paginate($filters),
            'filters' => $filters,
            'summary' => $this->service->summary($filters),
            'statuses' => BranchStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('branches::create', [
            'managers' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        $branch = $this->service->create($request->validated());

        return redirect()->route('branches.show', $branch)->with('status', 'Branch created and opened.');
    }

    public function show(Branch $branch): View
    {
        $branch->load(['manager']);

        return view('branches::show', ['branch' => $branch]);
    }

    public function edit(Branch $branch): View
    {
        return view('branches::edit', [
            'branch' => $branch,
            'managers' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $this->service->update($branch, $request->validated());

        return redirect()->route('branches.show', $branch)->with('status', 'Branch updated.');
    }

    public function open(Branch $branch): RedirectResponse
    {
        $this->service->open($branch);

        return redirect()->route('branches.show', $branch)->with('status', 'Branch opened.');
    }

    public function close(Branch $branch): RedirectResponse
    {
        $this->service->close($branch);

        return redirect()->route('branches.show', $branch)->with('status', 'Branch closed.');
    }

    public function switchBranch(Request $request, ?Branch $branch = null): RedirectResponse
    {
        if (! $branch || ! $branch->exists) {
            $branchId = $request->route('branch') ?? $request->query('branch');
            if ($branchId) {
                $branch = Branch::find($branchId);
            }
        }

        if ($branch && $branch->exists) {
            session(['current_branch_id' => $branch->id]);

            if ($request->query('redirect') === 'show') {
                return redirect()->route('branches.show', $branch)
                    ->with('status', __('Switched active branch to: :name', ['name' => $branch->name]));
            }

            return back()->with('status', __('Switched active branch to: :name', ['name' => $branch->name]));
        }

        session()->forget('current_branch_id');

        if ($request->query('redirect') === 'index') {
            return redirect()->route('branches.index')
                ->with('status', __('Viewing all branches.'));
        }

        return back()->with('status', __('Viewing all branches.'));
    }
}
