<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use App\Repositories\AttachmentRepository;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepository $repository
    ) {
    }

    public function uploadSingleImage(int $userId, UploadedFile $file)
    {
        $this->validateUploadLimit($userId, 1);

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
        $this->validateUploadLimit($userId, count($files));

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

    /**
     * Validate that the user has not exceeded the upload limit
     *
     * @param int $userId
     * @param int $newUploadsCount Number of new uploads being attempted
     * @throws ValidationException
     */
    private function validateUploadLimit(int $userId, int $newUploadsCount): void
    {
        $currentCount = $this->repository->countTodayByUserId($userId);
        $maxLimit = config('attachments.max_per_user', 1000);
        $remainingSlots = $maxLimit - $currentCount;

        if ($remainingSlots <= 0) {
            throw ValidationException::withMessages([
                'attachments' => ["You have reached the maximum upload limit of {$maxLimit} images. Please delete some images before uploading new ones."],
            ]);
        }

        if ($newUploadsCount > $remainingSlots) {
            throw ValidationException::withMessages([
                'attachments' => ["You can only upload {$remainingSlots} more image(s). You currently have {$currentCount} images and the limit is {$maxLimit}."],
            ]);
        }
    }
}
