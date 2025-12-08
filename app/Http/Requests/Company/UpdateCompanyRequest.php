<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->company !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'type' => ['required', Rule::in(['holder', 'lessee', 'both'])],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|size:2',
            'website' => 'nullable|url|max:255',
            'payout_method' => ['nullable', Rule::in(['bank_transfer', 'paypal', 'crypto'])],
            'payout_details' => 'nullable|array',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country')) {
            $this->merge([
                'country' => strtoupper($this->country),
            ]);
        }
    }
}
