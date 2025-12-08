<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CheckIpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'ip_address' => 'required|ip',
        ];
    }

    public function messages(): array
    {
        return [
            'ip_address.required' => __('IP address is required.'),
            'ip_address.ip' => __('Please enter a valid IP address.'),
        ];
    }
}
