<?php

namespace App\Http\Requests\Prompts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromptTemplateRequest extends FormRequest
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
        $promptTemplate = $this->route('promptTemplate');

        return [
            'key' => ['required', 'string', 'max:255', Rule::unique('prompt_templates', 'key')->ignore($promptTemplate?->getKey())],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'system_prompt' => ['nullable', 'string'],
            'user_prompt_template' => ['required', 'string'],
            'module' => ['required', 'string', 'max:100', Rule::in(['content', 'offers', 'plans', 'analysis'])],
            'version' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'key' => 'prompt key',
            'title' => 'title',
            'module' => 'module',
            'version' => 'version',
            'system_prompt' => 'system prompt',
            'user_prompt_template' => 'user prompt template',
            'is_active' => 'active status',
        ];
    }
}
