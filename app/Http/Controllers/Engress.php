<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\Document as DocumentDataTransferObject;
use App\Http\Requests\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Wnx\SidecarBrowsershot\BrowsershotLambda;

class Engress extends Controller
{
    /**
     * Responsible for making sidecar request, and sending contents to render.
     * Will return formatted response.
     *
     * 
     * @param Document $request
     * @return JsonResponse
     */
    public function consumeDocumentRequest(Document $request)
    {
        $document = new DocumentDataTransferObject(...$request->all());

        // Add loop to check we don't create duplicate files
        // as files with same name are replaced, and not duplicated in S3
        $randomlyGeneratedFilename = Str::random();
        while (Storage::disk('s3')->exists($randomlyGeneratedFilename)) {
            $randomlyGeneratedFilename = Str::random();
        }

        try {

            // Generate our document using Sidecar, AWS Lambda and Browsershot.
            $generatedDocument = BrowsershotLambda::html($document->contents)->pdf();

            // Take binary data, and dump it into S3, with or without a filepath,
            if ($document->path) {
                Storage::disk('s3')->put("{$document->path}/$randomlyGeneratedFilename.pdf", $generatedDocument);
            } else {
                Storage::disk('s3')->put("$randomlyGeneratedFilename.pdf", $generatedDocument);
            }

            return response()->json([
                'id' => $document->id,
                'name' => $document->name,
                'filename' => $randomlyGeneratedFilename,
                'path' => $document->path
            ]);

        } catch (\Exception $e) {
            return response(500)->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
