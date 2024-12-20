<?php

namespace TechStudio\Core\app\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use TechStudio\Core\app\Rules\NationalCodeRule;

class UpdateProfileRequest extends FormRequest
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
            'firstName' => ['string'],
            'lastName' => ['string'],
            'email' => [Rule::unique('core_user_profiles')->ignore($this->email,'email')],
            'nationalCode' => [new NationalCodeRule],
            'shopLink' => ['nullable', 'url:http,https'],
            'avatarUrl' => [
                'nullable', 
                'url', 
                function ($attribute, $value, $fail) {
                    $allowedDomain = rtrim(env('MINIO_SERVER_URL'), '/');
                    
                    if (!str_starts_with($value, $allowedDomain)) {
                        $fail("The {$attribute} must be a valid URL from the allowed domain: {$allowedDomain}");
                    }
                },
            ],
        ];
    }
}
