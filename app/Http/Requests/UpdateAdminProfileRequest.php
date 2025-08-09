<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateAdminProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // Password lama hanya wajib jika ingin mengubah password baru
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