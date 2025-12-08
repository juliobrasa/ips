<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveAbuseReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'resolution_notes' => 'required|string|max:2000',
            'action' => ['required', Rule::in(['resolved', 'dismissed', 'escalated'])],
            'notify_holder' => 'boolean',
            'notify_lessee' => 'boolean',
            'suspend_subnet' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => __('Resolution notes are required.'),
            'action.required' => __('Please select an action.'),
        ];
    }
}
