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
                'title' => $this->category->title ?? null,
                'slug' => $this->category->slug ?? null,
            ],
            'status' => $this->status,
            'creationDate' => $this->created_at,
        ];
    }
}
