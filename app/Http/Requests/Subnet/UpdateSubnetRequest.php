<?php

namespace App\Http\Requests\Subnet;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubnetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('subnet'));
    }

    public function rules(): array
    {
        return [
            'geolocation_country' => 'nullable|string|size:2',
            'geolocation_city' => 'nullable|string|max:100',
            'price_per_ip_monthly' => 'required|numeric|min:0.01|max:100',
            'min_lease_months' => 'required|integer|min:1|max:36',
            'description' => 'nullable|string|max:1000',
            'rpki_delegated' => 'boolean',
            'auto_renewal' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('geolocation_country')) {
            $this->merge([
                'geolocation_country' => strtoupper($this->geolocation_country),
            ]);
        }
    }
}
