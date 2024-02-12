<?php

namespace TechStudio\Core\app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends CreateUserRequest
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
        return array_merge(parent::rules(), [
            'email' => ['required', Rule::unique('user_profiles')->ignore($this->email)],
            'phoneNumber' => ['required', Rule::unique('user_profiles')->ignore($this->phoneNumber)],
        ]);
    }
}
