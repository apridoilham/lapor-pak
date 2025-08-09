<?php

namespace App\Http\Requests;

use App\Models\Resident; // <-- DITAMBAHKAN
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResidentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // PERUBAHAN DI SINI:
        // Ambil objek Resident dari database menggunakan ID dari route, lalu ambil user_id-nya.
        $resident = Resident::find($this->route('resident'));
        $userId = $resident ? $resident->user_id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // Pastikan $userId tidak null sebelum digunakan
                Rule::unique('users')->ignore($userId),
            ],
            'avatar' => 'nullable|file|image|max:2048',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}