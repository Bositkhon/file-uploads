<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchUploadImageRequest;
use App\Http\Resources\AttachmentResource;
use App\Services\AttachmentService;

class BatchUploadImageAction extends Controller
{
    public function __construct(
        private readonly AttachmentService $attachmentService
    ) {
    }

    public function __invoke(BatchUploadImageRequest $request)
    {
        $attachments = $this->attachmentService->batchUploadImages(
            userId: $request->user()->id,
            files: $request->file('attachments'),
        );

        return AttachmentResource::collection($attachments);
    }
}
