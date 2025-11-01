<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportTranslationRequest extends FormRequest
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
            "language_code" => "required|string|exists:languages,code",
            "tags" => "sometimes|array",
            "tags.*" => "string|max:50",
            // "format" => "sometimes|string|in:flat,nested,grouped", // response data structure improvements
            "include_empty" => "sometimes|boolean",
        ];
    }

    public function messages()
    {
        return [
            "language_code.required" => "Language code is required for export",
            "language_code.exists" => "The selected language code is invalid",
        ];
    }
}
