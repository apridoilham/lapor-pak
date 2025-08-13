<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'super-admin']);
    }

    public function rules(): array
    {
        return [
            'avatar' => 'nullable|file|image|max:2048',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}