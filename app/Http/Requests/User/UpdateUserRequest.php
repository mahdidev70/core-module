<?php

namespace TechStudio\Blog\app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends CreateUserRequest
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
            'email' => [Rule::unique('user_profiles')->ignore($this->email,'email')],
            // 'phoneNumber' => [Rule::unique('user_profiles,','registration_phone_number')->ignore($this->registration_phone_number)],
        ]);
    }
}
