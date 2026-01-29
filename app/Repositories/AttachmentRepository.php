<?php

namespace App\Repositories;

use App\Models\Attachment;

class AttachmentRepository
{
    public function create(
        int $userId,
        string $path,
        string $originalName,
        string $extension,
        string $mime,
        int $size
    ) {
        return Attachment::create([
            'user_id' => $userId,
            'path' => $path,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime' => $mime,
            'size' => $size,
        ]);
    }

    public function findAllImagesByUserId(int $userId)
    {
        return Attachment::query()
            ->where('user_id', $userId)
            ->whereIn('mime', ['image/jpeg', 'image/png'])
            ->get();
    }

    public function findByIdAndUserId(int $id, int $userId)
    {
        return Attachment::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function deleteById(int $id)
    {
        return Attachment::where('id', $id)->delete();
    }
}
