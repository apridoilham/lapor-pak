<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => 'nullable|file|image|max:2048',

            // PERUBAHAN DI SINI:
            // 'current_password' sekarang hanya wajib jika 'password' (baru) diisi.
            'current_password' => [
                'nullable',
                Rule::requiredIf(fn () => $this->filled('password')), // Dihapus: || $this->input('email') !== $user->email
                function ($attribute, $value, $fail) use ($user) {
                    if ($this->filled('current_password') && !Hash::check($value, $user->password)) {
                        $fail('Password lama yang Anda masukkan salah.');
                    }
                },
            ],

            'password' => [
                'nullable',
                'required_with:current_password',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    /**
     * Pesan kustom untuk aturan validasi.
     */
    public function messages(): array
    {
        return [
            // PERUBAHAN DI SINI: Teks pesan disederhanakan.
            'current_password.required' => 'Password lama wajib diisi untuk mengubah password baru.',
            'password.required_with' => 'Password baru wajib diisi jika Anda ingin mengubah password.',
        ];
    }
}