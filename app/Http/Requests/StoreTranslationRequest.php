<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
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
            "key" => "required|string|max:255",
            "description" => "nullable|string|max:500",
            "translations" => "required|array|min:1",
            "translations.*.language_code" => "required|string|exists:languages,code",
            "translations.*.value" => "required|string",
            "tags" => "sometimes|array",
            "tags.*" => "string|max:50"
        ];
    }

    public function messages()
    {
        return [
            "key.required" => "Translation key is required",
            "translations.required" => "At least one translation is required",
            "translations.*.language_code.exists" => "The specified language does not exist",
            "translations.*.value.required" => "Translation value is required for all languages",
        ];
    }
}
