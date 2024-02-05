<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'answer' => $this->answer,
            'category' => [
                'id' => $this->category->id ?? null,
                'title' => $this->category->title ?? null,
                'slug' => $this->category->slug ?? null,
                'avatarUrl' => $this->category->avatar_url ?? null,
            ],
            'categoryId' => $this->category->id ?? null,
            'isFrequent' => $this->is_frequent,
            'status' => $this->status,
            'creationDate' => $this->created_at,
        ];
    }
}
