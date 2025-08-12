<?php

namespace App\Http\Requests;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
        $rules = [
            'report_category_id' => 'required|exists:report_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'image' => 'required|file|image|max:2048',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string|max:500',
        ];

        if ($this->user()->hasRole('resident')) {
            $rules['visibility'] = ['required', Rule::enum(ReportVisibilityEnum::class)];
            $rules['resident_id'] = 'nullable|exists:residents,id';
        } else {
            $rules['resident_id'] = 'required|exists:residents,id';
        }

        return $rules;
    }
}