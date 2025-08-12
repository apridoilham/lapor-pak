<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('email_username')) {
            $this->merge([
                'email' => $this->email_username . '@bsblapor.com',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email_username' => 'required|string|alpha_num',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'avatar' => 'required|file|image|max:2048',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
        ];
    }
}