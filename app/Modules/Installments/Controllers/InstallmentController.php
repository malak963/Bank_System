<?php

namespace App\Modules\Installments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Installments\Requests\IndexInstallmentRequest;
use App\Modules\Installments\Services\InstallmentService;
use Illuminate\Contracts\View\View;

class InstallmentController extends Controller
{
    public function __construct(private InstallmentService $service) {}

    public function index(IndexInstallmentRequest $request): View
    {
        $filters = $request->filters();

        return view('installments::index', [
            'installments' => $this->service->paginate($filters),
            'summary' => $this->service->summary(),
            'filters' => $filters,
        ]);
    }
}
