<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\AttachmentRepository;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepository $repository
    ) {
    }

    public function uploadSingleImage(int $userId, UploadedFile $file)
    {
        $path = $file->store('attachments');

        return $this->repository->create(
            userId: $userId,
            path: $path,
            originalName: $file->getClientOriginalName(),
            extension: $file->getClientOriginalExtension(),
            mime: $file->getMimeType(),
            size: $file->getSize(),
        );
    }

    /**
     * Summary of batchUploadImages
     * @param int $userId
     * @param array<int, UploadedFile> $files
     * @return Collection<int, Attachment>
     */
    public function batchUploadImages(int $userId, array $files): Collection
    {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store('attachments');

            $attachments[] = [
                'user_id' => $userId,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];
        }

        return DB::transaction(fn () => collect($attachments)->map(fn ($attachment) => $this->repository->create(
            userId: $attachment['user_id'],
            path: $attachment['path'],
            originalName: $attachment['original_name'],
            extension: $attachment['extension'],
            mime: $attachment['mime'],
            size: $attachment['size'],
        )));
    }
}
