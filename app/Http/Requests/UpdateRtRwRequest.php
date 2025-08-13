<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRtRwRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // TAMBAHKAN BLOK KODE INI
        if ($this->has('number')) {
            $this->merge([
                'number' => str_pad($this->number, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rwId = $this->route('rw')->id;

        return [
            'number' => [
                'required',
                'string',
                'digits:3',
                Rule::unique('rws', 'number')->ignore($rwId),
            ],
            'rt_count' => 'required|integer|min:1|max:99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'number.required' => 'Nomor RW wajib diisi.',
            'number.digits' => 'Nomor RW harus terdiri dari 3 digit.',
            'number.unique' => 'Nomor RW ini sudah digunakan.',
            'rt_count.required' => 'Jumlah RT wajib diisi.',
            'rt_count.integer' => 'Jumlah RT harus berupa angka.',
            'rt_count.min' => 'Jumlah RT minimal harus 1.',
            'rt_count.max' => 'Jumlah RT tidak boleh lebih dari 99.',
        ];
    }
}