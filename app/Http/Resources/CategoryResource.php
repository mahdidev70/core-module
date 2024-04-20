<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Blog\app\Http\Resources\AthorResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'order' => $this->order,
            'avatarUrl' => $this->avatar_url,
            'status' => $this->status,
            'creationDate' => $this->created_at,
            'faqCount' => $this->faq->where('status', 'active')->count() ?? null,
            'chatroomCount' => $this->chat_room_count ?? null,
            'questionCount' => $this->questions_count ?? null
        ];
    }
}
