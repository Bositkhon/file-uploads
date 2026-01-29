<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'path' => $this->path,
            'original_name' => $this->original_name,
            'mime' => $this->mime,
            'size' => $this->size,
            'download_url' => asset('storage/' . $this->path),
        ];
    }
}
