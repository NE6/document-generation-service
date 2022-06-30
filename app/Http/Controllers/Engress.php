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
     * @param Document $request
     * @return JsonResponse
     */
    public function generateAndSaveDocument(Document $request): JsonResponse
    {

        $document = $this->processDocumentRequest($request);
        $randomlyGenerateUniqueFilename = $this->generateUniqueFilename();

        try {

            // Generate our document using Sidecar, AWS Lambda and Browsershot.
            $generatedDocument = BrowsershotLambda::html($document->contents)->pdf();

            // Take binary data, and dump it into S3, with or without a filepath,
            if ($document->path) {
                Storage::disk('s3')->put("{$document->path}/$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
            } else {
                Storage::disk('s3')->put("$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
            }

            return response()->json([
                'id' => $document->id,
                'name' => $document->name,
                'filename' => $randomlyGenerateUniqueFilename,
                'path' => $document->path
            ]);

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Will return a document data transfer object for user later on.
     *
     * @param Document $request
     * @return DocumentDataTransferObject
     */
    private function processDocumentRequest(Document $request)
    {
        return new DocumentDataTransferObject(...$request->all());
    }

    /**
     * Will return a unique filename, so there's no risk of any collisions.
     *
     * @return string
     */
    private function generateUniqueFilename(): string
    {
        // Add loop to check we don't create duplicate files
        // as files with same name are replaced, and not duplicated in S3

        $randomlyGenerateUniqueFilename = Str::random();
        while (Storage::disk('s3')->exists($randomlyGenerateUniqueFilename)) {
            $randomlyGenerateUniqueFilename = Str::random();
        }

        return $randomlyGenerateUniqueFilename;
    }
}
