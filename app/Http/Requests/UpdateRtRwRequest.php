<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRtRwRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('number')) {
            $this->merge([
                'number' => str_pad($this->number, 2, '0', STR_PAD_LEFT)
            ]);
        }
    }

    public function rules(): array
    {
        $rwId = $this->route('rtrw')->id;

        return [
            'number' => [
                'required',
                'string',
                'digits:2',
                Rule::unique('rws', 'number')->ignore($rwId),
            ],
            // PERUBAHAN DI SINI: integer -> numeric
            'rt_count' => 'required|numeric|min:1|max:99',
        ];
    }
}