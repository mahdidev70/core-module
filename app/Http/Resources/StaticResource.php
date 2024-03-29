<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Blog\app\Http\Resources\AthorResource;

class StaticResource extends JsonResource
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
            'key' => $this->key,
            'text' => $this->text,
            'fileUrl' => $this->file_url,
        ];
    }
}
