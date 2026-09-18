<?php

namespace App\Modules\Branches\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use App\Modules\Branches\Requests\IndexBranchRequest;
use App\Modules\Branches\Requests\StoreBranchRequest;
use App\Modules\Branches\Requests\UpdateBranchRequest;
use App\Modules\Branches\Services\BranchService;
use App\Modules\Users\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
}
