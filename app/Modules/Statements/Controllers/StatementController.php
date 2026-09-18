<?php

namespace App\Modules\Statements\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Statements\Services\StatementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatementController extends Controller
{
    public function __construct(
        private StatementService $statementService
    ) {}

    public function index(Request $request)
    {
        $statements = $this->statementService->getAllStatements($request->all());
        return view('statements::index', compact('statements'));
    }

    public function create()
    {
        return view('statements::create');
    }

    public function store(Request $request)
    {
        $statement = $this->statementService->createStatement($request->all());
        return redirect()->route('statements.show', $statement->id)
            ->with('success', 'Statement created successfully');
    }

    public function show($id)
    {
        $statement = $this->statementService->getStatement($id);
        return view('statements::show', compact('statement'));
    }

    public function generate($id)
    {
        $statement = $this->statementService->generateStatementFile($id);
        return redirect()->route('statements.show', $statement->id)
            ->with('success', 'Statement file generated successfully');
    }

    public function download($id)
    {
        $statement = $this->statementService->getStatement($id);
        
        if (!$statement->file_path || !Storage::disk('local')->exists($statement->file_path)) {
            return back()->with('error', 'File not found');
        }

        return Storage::disk('local')->download($statement->file_path);
    }

    public function destroy($id)
    {
        $this->statementService->deleteStatement($id);
        return redirect()->route('statements.index')
            ->with('success', 'Statement deleted successfully');
    }
}
