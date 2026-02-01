<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class DocumentIndexRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:200'],
            'author_id' => ['nullable', 'integer', 'min:1', 'exists:authors,id'],
            'faculty_id' => ['nullable', 'integer', 'min:1', 'exists:faculties,id'],
            'study_program_id' => ['nullable', 'integer', 'min:1', 'exists:study_programs,id'],
            'document_type_id' => ['nullable', 'integer', 'min:1', 'exists:document_types,id'],
            'category_id' => ['nullable', 'integer', 'min:1', 'exists:categories,id'],
            'publish_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.max' => 'Pencarian maksimal 200 karakter.',
        ];
    }
}
