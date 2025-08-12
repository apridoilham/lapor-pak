<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResidentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('email_username')) {
            $this->merge([
                'email_username' => strtolower($this->email_username),
                'email' => strtolower($this->email_username) . '@bsblapor.com',
            ]);
        }
    }
    
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'super-admin']);
    }

    public function rules(): array
    {
        $residentId = $this->route('resident');
        $user = \App\Models\Resident::find($residentId)->user;

        return [
            'name' => 'required|string|max:255',
            'email_username' => 'required|string|alpha_num',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => 'nullable|file|image|max:2048',
            'rt_id' => 'required|exists:rts,id',
            'rw_id' => 'required|exists:rws,id',
            'address' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed',
        ];
    }
}