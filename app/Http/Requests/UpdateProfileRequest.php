<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'avatar'  => 'nullable|file|image|max:2048',
            'phone'   => 'nullable|string|digits_between:10,15',
            
            'rt_id'   => 'required|exists:rts,id',
            'rw_id'   => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
        ];
    }
}