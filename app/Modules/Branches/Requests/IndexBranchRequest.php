<?php

namespace App\Modules\Branches\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,closed'],
            'sort' => ['nullable', 'in:latest,name'],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => $this->input('search'),
            'status' => $this->input('status'),
            'sort' => $this->input('sort', 'latest'),
        ];
    }
}
