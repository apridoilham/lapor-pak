<?php

namespace App\Http\Requests;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
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
            'visibility' => ['required', Rule::enum(ReportVisibilityEnum::class)],
        ];

        if (auth()->user()->hasRole('admin')) {
            $rules['resident_id'] = 'required|exists:residents,id';
        } else {
            $rules['resident_id'] = 'nullable|exists:residents,id';
        }

        return $rules;
    }
}