<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email_username')) {
            $this->merge([
                'email_username' => strtolower($this->email_username),
                'email' => strtolower($this->email_username) . '@bsblapor.com',
            ]);
        }
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => 'required|string|max:255',
            'email_username' => 'required|string|alpha_num',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'avatar' => 'nullable|file|image|max:2048',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
            'current_password' => [
                'nullable',
                'required_with:password',
                function ($attribute, $value, $fail) {
                    if ($this->filled('current_password') && !Hash::check($value, $this->user()->password)) {
                        $fail('Password lama yang Anda masukkan salah.');
                    }
                },
            ],
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}