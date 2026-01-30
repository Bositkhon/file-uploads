<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Repositories\AttachmentRepository;

class AttachmentService
{
    private const EXTENSION_TO_MIME = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    public function __construct(
        private readonly AttachmentRepository $repository
    ) {
    }

    public function uploadSingleImage(int $userId, UploadedFile $file)
    {
        $this->validateUploadLimit($userId, 1);

        $contentHash = hash_file('sha256', $file->getRealPath());
        $existing = $this->repository->findByContentHashAndUserId($contentHash, $userId);
        if ($existing) {
            return $existing;
        }

        $extension = $file->getClientOriginalExtension() ?: $this->guessExtensionFromMime($file->getMimeType());
        $path = $file->storeAs('attachments', Str::random(40) . '.' . $extension);

        return $this->repository->create(
            userId: $userId,
            path: $path,
            originalName: $file->getClientOriginalName(),
            extension: $extension,
            mime: $this->getEffectiveMimeType($file),
            size: $file->getSize(),
            contentHash: $contentHash,
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

        $results = collect();

        foreach ($files as $file) {
            $contentHash = hash_file('sha256', $file->getRealPath());
            $existing = $this->repository->findByContentHashAndUserId($contentHash, $userId);
            if ($existing) {
                $results->push($existing);
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: $this->guessExtensionFromMime($file->getMimeType());
            $path = $file->storeAs('attachments', Str::random(40) . '.' . $extension);
            $attachment = $this->repository->create(
                userId: $userId,
                path: $path,
                originalName: $file->getClientOriginalName(),
                extension: $extension,
                mime: $this->getEffectiveMimeType($file),
                size: $file->getSize(),
                contentHash: $contentHash,
            );
            $results->push($attachment);
        }

        return $results;
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
        $maxLimit = config('attachments.max_per_user', 1);
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

    /**
     * Use client's original extension to determine MIME when client sends generic type (e.g. octet-stream).
     */
    private function getEffectiveMimeType(UploadedFile $file): string
    {
        $reported = $file->getMimeType();
        if ($reported !== 'application/octet-stream' && $reported !== null) {
            return $reported;
        }
        $ext = strtolower($file->getClientOriginalExtension());
        return self::EXTENSION_TO_MIME[$ext] ?? $reported ?? 'application/octet-stream';
    }

    private function guessExtensionFromMime(?string $mime): string
    {
        if ($mime === null) {
            return 'bin';
        }
        $map = array_flip(self::EXTENSION_TO_MIME);
        return $map[$mime] ?? 'bin';
    }
}
