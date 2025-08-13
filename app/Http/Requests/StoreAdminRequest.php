<?php

namespace App\Http\Requests;

use App\Rules\UniqueUserRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', new UniqueUserRole()],
            'rw_id' => 'required|exists:rws,id',
        ];
    }
}