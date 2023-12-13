<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Blog\app\Http\Resources\AthorResource;

class CommentArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userId' => $this->user ->id,
            'title' => $this->text,
            'article' => [
                'title' => $this->article->title,
                'slug' => $this->article->slug,
            ],
            'createdAt' => $this->created_at,
            'author' => new AthorResource($this->user),
            'status' => $this->status,
            'articleStatus' => $this->article->status,
        ];
    }
}
