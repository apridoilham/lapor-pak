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
            $cleanedNumber = ltrim($this->number, '0');
            $this->merge(['number' => empty($cleanedNumber) && $this->number === '0' ? '0' : $cleanedNumber]);
        }
        if ($this->has('rt_count')) {
            $this->merge(['rt_count' => ltrim($this->rt_count, '0')]);
        }
    }

    public function rules(): array
    {
        $rwId = $this->route('rtrw')->id;

        return [
            'number' => [
                'required',
                'string',
                'numeric',
                Rule::unique('rws', 'number')->ignore($rwId),
            ],
            'rt_count' => 'required|integer|min:1|max:99',
        ];
    }
}