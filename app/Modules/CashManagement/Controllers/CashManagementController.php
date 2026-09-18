<?php

namespace App\Modules\CashManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\CashManagement\Models\CashOperation;
use App\Modules\CashManagement\Services\CashManagementService;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    public function __construct(
        private CashManagementService $cashManagementService
    ) {}

    public function index(Request $request)
    {
        $operations = $this->cashManagementService->getAllOperations($request->all());
        return view('cash-management::index', compact('operations'));
    }

    public function create()
    {
        return view('cash-management::create');
    }

    public function store(Request $request)
    {
        $operation = $this->cashManagementService->createOperation($request->all());
        return redirect()->route('cash-management.show', $operation->id)
            ->with('success', 'Cash operation created successfully');
    }

    public function show($id)
    {
        $operation = $this->cashManagementService->getOperation($id);
        return view('cash-management::show', compact('operation'));
    }

    public function edit($id)
    {
        $operation = $this->cashManagementService->getOperation($id);
        return view('cash-management::edit', compact('operation'));
    }

    public function update(Request $request, $id)
    {
        $operation = $this->cashManagementService->updateOperation($id, $request->all());
        return redirect()->route('cash-management.show', $operation->id)
            ->with('success', 'Cash operation updated successfully');
    }

    public function destroy($id)
    {
        $this->cashManagementService->deleteOperation($id);
        return redirect()->route('cash-management.index')
            ->with('success', 'Cash operation deleted successfully');
    }
}
