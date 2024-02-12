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
            'answers' => $this->answer,
            'category' => new CategoryResource($this->category) ?? null,
            'categoryId' => $this->category->id ?? null,
            'isFrequent' => $this->is_frequent,
            'status' => $this->status,
            'creationDate' => $this->created_at,
        ];
    }
}
