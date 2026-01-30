<?php

namespace App\Repositories;

use App\Models\Attachment;

class AttachmentRepository
{
    public function findByContentHashAndUserId(string $contentHash, int $userId): ?Attachment
    {
        return Attachment::query()
            ->where('user_id', $userId)
            ->where('content_hash', $contentHash)
            ->first();
    }

    public function create(
        int $userId,
        string $path,
        string $originalName,
        string $extension,
        string $mime,
        int $size,
        ?string $contentHash = null
    ) {
        return Attachment::create([
            'user_id' => $userId,
            'path' => $path,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime' => $mime,
            'size' => $size,
            'content_hash' => $contentHash,
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

    public function countTodayByUserId(int $userId): int
    {
        return Attachment::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', today())
            ->whereIn('mime', ['image/jpeg', 'image/png'])
            ->count();
    }
}
