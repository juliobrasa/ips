<?php

namespace App\Http\Requests\Lease;

use Illuminate\Foundation\Http\FormRequest;

class AssignAsnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lease = $this->route('lease');
        return $lease && $lease->lesseeCompany->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'asn' => [
                'required',
                'regex:/^AS\d{1,10}$/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asn.required' => __('ASN is required.'),
            'asn.regex' => __('ASN must be in format AS followed by numbers (e.g., AS12345).'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('asn')) {
            $this->merge([
                'asn' => strtoupper($this->asn),
            ]);
        }
    }
}
