<?php

namespace TechStudio\Core\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TechStudio\Core\app\Models\Follow;

class StoreFollowRequest extends FormRequest
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
            'followId' => [
                'required',
                'integer',
                'exists:users,id', // Validate that the user being followed exists
                // Custom rule to prevent following the same user twice
                function ($attribute, $value, $fail) {
                    $followerId = auth()->id();
                    if ($this->do == 'follow' && Follow::where('follower_id', $followerId)->where('following_id', $value)->exists()) {
                        $fail('شما قبلاً این کاربر را فالو کرده‌اید.');
                    }
                },
                // Custom rule to ensure the user has followed the target user before unfollowing
                function ($attribute, $value, $fail) {
                    $followerId = auth()->id();
                    if ($this->do == 'unfollow' && !Follow::where('follower_id', $followerId)->where('following_id', $value)->exists()) {
                        $fail('شما این کاربر را فالو نکرده‌اید.');
                    }
                },
            ],
            'do' => 'required|in:follow,unfollow',
        ];
    }
}
