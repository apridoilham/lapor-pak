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

    public function rules(): array
    {
        $rwId = $this->route('rtrw')->id;

        return [
            'number' => [
                'required',
                'string',
                'digits:3',
                Rule::unique('rws', 'number')->ignore($rwId),
            ],
            'rt_count' => 'required|integer|min:1|max:99',
        ];
    }
}