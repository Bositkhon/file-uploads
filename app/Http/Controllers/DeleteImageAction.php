<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AttachmentRepository;

class DeleteImageAction extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, AttachmentRepository $repository, int $id)
    {
        $attachment = $repository->findByIdAndUserId(
            id: $id,
            userId: $request->user()->id,
        );

        if (!$attachment) {
            return response()->json([
                'message' => 'Attachment not found',
            ], 404);
        }

        $repository->deleteById($id);

        return response()->noContent();
    }
}
