<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewTranslationRequest extends FormRequest
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
            "search" => "sometimes|string|max:255",
            "tags" => "sometimes|array",
            "tags.*" => "string|max:50",
            "language" => "sometimes|string|exists:languages,code",
            "per_page" => "sometimes|integer|min:1|max:100",
            "page" => "sometimes|integer|min:1",
            "sort_by" => "sometimes|string|in:key,created_at,updated_at",
            "sort_order" => "sometimes|string|in:asc,desc",
        ];
    }
}
