<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttachmentResource;
use App\Repositories\AttachmentRepository;
use Illuminate\Http\Request;

class ListImagesAction extends Controller
{
    public function __invoke(Request $request, AttachmentRepository $repository)
    {
        $images = $repository->findAllImagesByUserId($request->user()->id);

        return AttachmentResource::collection($images);
    }
}
