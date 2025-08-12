<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('email_username')) {
            $this->merge([
                'email' => $this->email_username . '@bsblapor.com',
            ]);
        }
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        $userId = $this->route('admin_user')->id;

        return [
            'name' => 'required|string|max:255',
            'email_username' => 'required|string|alpha_num',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'rw_id' => 'required|exists:rws,id',
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}