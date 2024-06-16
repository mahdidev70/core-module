<?php

namespace TechStudio\Core\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUpdateBannerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'linkUrl' => ['nullable', 'string'],
            'imageUrl' => ['required', 'string'],
            'date' => ['nullable', 'date'],
            'price' => ['nullable', 'integer'],
            'type' => ['nullable', 'in:event, banner'],
            'status' => ['nullable', 'in:published,draft,delete'],
        ];
    }
}
