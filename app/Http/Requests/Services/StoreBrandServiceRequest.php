<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBrandServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('brand_services', 'slug')],
            'description' => ['nullable', 'string'],
            'audience' => ['nullable', 'string', 'max:255'],
            'benefits_json' => ['nullable', 'string'],
            'problems_solved_json' => ['nullable', 'string'],
            'pricing_notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:50', Rule::in(['draft', 'active', 'archived'])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
