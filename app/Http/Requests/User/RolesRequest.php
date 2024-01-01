<?php

namespace TechStudio\Blog\app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RolesRequest extends FormRequest
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
                'userIds' => ['required','array'],
                'userIds.*' => ['integer','exists:user_profiles,id'],
                'roles' => ['required','array'],
                'roles.*' => ['integer','exists:roles,id'],
        ];
    }
}
