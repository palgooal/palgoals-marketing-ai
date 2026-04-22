<?php

namespace App\Http\Requests\Offers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOfferGenerationRequest extends FormRequest
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
            'prompt_template_id' => ['required', 'integer', 'exists:prompt_templates,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'offer_type' => ['required', 'string', 'in:limited_time_offer,bundle_offer,seasonal_offer,discount_offer'],
            'input_payload' => ['nullable', 'string'],
            'context' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $inputPayload = (string) $this->input('input_payload', '');

            if (trim($inputPayload) === '') {
                return;
            }

            try {
                $decoded = json_decode($inputPayload, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $validator->errors()->add('input_payload', 'The input payload must be valid JSON.');

                return;
            }

            if (! is_array($decoded) || array_is_list($decoded)) {
                $validator->errors()->add('input_payload', 'The input payload must be a JSON object.');
            }
        });
    }
}
