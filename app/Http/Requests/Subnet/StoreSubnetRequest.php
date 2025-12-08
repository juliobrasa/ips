<?php

namespace App\Http\Requests\Subnet;

use App\Models\Subnet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubnetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->company;
        return $company && $company->canList();
    }

    public function rules(): array
    {
        return [
            'ip_address' => [
                'required',
                'ip',
                function ($attribute, $value, $fail) {
                    $exists = Subnet::where('ip_address', $value)
                        ->where('cidr', $this->cidr)
                        ->exists();
                    if ($exists) {
                        $fail(__('This subnet is already registered in the system.'));
                    }
                },
            ],
            'cidr' => 'required|integer|min:16|max:24',
            'rir' => ['required', Rule::in(['RIPE', 'ARIN', 'LACNIC', 'APNIC', 'AFRINIC'])],
            'geolocation_country' => 'nullable|string|size:2',
            'geolocation_city' => 'nullable|string|max:100',
            'price_per_ip_monthly' => 'required|numeric|min:0.01|max:100',
            'min_lease_months' => 'required|integer|min:1|max:36',
            'description' => 'nullable|string|max:1000',
            'rpki_delegated' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ip_address.required' => __('The IP address is required.'),
            'ip_address.ip' => __('Please enter a valid IP address.'),
            'cidr.required' => __('The CIDR prefix is required.'),
            'cidr.min' => __('CIDR must be at least /16.'),
            'cidr.max' => __('CIDR must be at most /24.'),
            'rir.required' => __('Please select a Regional Internet Registry.'),
            'price_per_ip_monthly.required' => __('Please set a monthly price per IP.'),
            'price_per_ip_monthly.min' => __('Price must be at least $0.01.'),
            'min_lease_months.required' => __('Please set a minimum lease duration.'),
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
