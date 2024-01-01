<?php

namespace TechStudio\Blog\app\Http\Requests\User;

use App\Http\Requests\User\BaseUserValidationRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        return  [
            'firstName' => ['required','string'],
            'lastName' => ['required','string'],
            'email' => ['required_if:phoneNumber,null','nullable','email','unique:user_profiles,email'],
            'phoneNumber' => ['required_if:email,null','nullable','numeric'],
            // 'email_or_phoneNumber' => 'required_without_all:email,phoneNumber',
            'avatarUrl' => ['nullable'],
            'password' => ['required'],
            'role' => ['nullable','integer','exists:roles,id'],
        ];
    }

}
