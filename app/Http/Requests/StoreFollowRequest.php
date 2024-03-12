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
                'exists:users,id', 
                function ($attribute, $value, $fail) {
                    $followerId = auth()->id();
                    if ($followerId == $value) {
                        $fail('شما نمیتوانید خودتان را فالو کنید');
                    }
                },
                function ($attribute, $value, $fail) {
                    $followerId = auth()->id();
                    if ($this->do == 'follow' && Follow::where('follower_id', $followerId)->where('following_id', $value)->exists()) {
                        $fail('شما قبلاً این کاربر را فالو کرده‌اید.');
                    }
                },
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
