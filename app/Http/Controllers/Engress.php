<?php

namespace App\Http\Controllers;

use App\Actions\DecryptEncryptedApiParameters;
use App\DataTransferObjects\Document as DocumentDataTransferObject;
use App\Http\Requests\DocumentSaveRequest;
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
     * @param  DocumentSaveRequest  $request
     * @return JsonResponse
     */
    public function generateAndSaveDocument(DocumentSaveRequest $request): JsonResponse
    {
        $document = DecryptEncryptedApiParameters::execute($this->processDocumentRequest($request));
        try {
            // Generate our document using Sidecar, AWS Lambda and Browsershot.
            $generatedDocument = BrowsershotLambda::html($document->contents)->pdf();
            $filename = $this->saveDocument($document, $generatedDocument);

            return response()->json([
                'id' => $document->id,
                'name' => $document->name,
                'filename' => $filename,
                'path' => $document->path,
            ]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Will return a document data transfer object for user later on.
     *
     * @param  DocumentSaveRequest  $request
     * @return DocumentDataTransferObject
     */
    private function processDocumentRequest(DocumentSaveRequest $request)
    {
        return new DocumentDataTransferObject(...$request->all());
    }

    /**
     * Will return a unique filename, so there's no risk of any collisions.
     *
     * @param  Storage|null  $disk
     * @return string
     */
    private function generateUniqueFilename(Storage $disk = null): string
    {
        // Add loop to check we don't create duplicate files
        // as files with same name are replaced, and not duplicated in S3
        $randomlyGenerateUniqueFilename = Str::random();

        // Check if we're using a dynamic disk.
        if ($disk) {
            while ($disk->exists($randomlyGenerateUniqueFilename)) {
                $randomlyGenerateUniqueFilename = Str::random();
            }
        } else {
            while (Storage::exists($randomlyGenerateUniqueFilename)) {
                $randomlyGenerateUniqueFilename = Str::random();
            }
        }

        return $randomlyGenerateUniqueFilename;
    }

    /**
     * Will save the newly created document.
     *
     * @param  DocumentDataTransferObject  $document
     * @param $generatedDocument
     * @return string
     */
    private function saveDocument(DocumentDataTransferObject $document, $generatedDocument): string
    {
        // Check if we're using dynamic S3. If we are, we must pass in our AWS creds.
        if ($document->aws_key) {
            $randomlyGenerateUniqueFilename = $this->saveDocumentOnDynamicS3Disk($document, $generatedDocument);
        } else {
            $randomlyGenerateUniqueFilename = $this->saveDocumentOnDefaultDisk($document, $generatedDocument);
        }

        return $randomlyGenerateUniqueFilename;
    }

    /**
     * Supports saving against any S3 bucket, as long as credentials are passed in. Doesn't have
     * to be the S3 bucket in our .env file.
     *
     * @param  DocumentDataTransferObject  $document
     * @param $generatedDocument
     * @return string
     */
    private function saveDocumentOnDynamicS3Disk(DocumentDataTransferObject $document, $generatedDocument)
    {
        $disk = Storage::createS3Driver([
            'key'    => $document->key,
            'secret' => $document->secret,
            'region' => $document->region,
            'bucket' => $document->bucket,
        ]);

        $randomlyGenerateUniqueFilename = $this->generateUniqueFilename($disk);

        // Take binary data, and save with or without a filepath
        if ($document->path) {
            $disk->put("{$document->path}/$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
        } else {
            $disk->put("$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
        }

        return $randomlyGenerateUniqueFilename;
    }

    /**
     * Will save a document against the default laravel filesystem.
     *
     * @param  DocumentDataTransferObject  $document
     * @param $generatedDocument
     * @return string
     */
    private function saveDocumentOnDefaultDisk(DocumentDataTransferObject $document, $generatedDocument)
    {
        // Get unique file name.
        $randomlyGenerateUniqueFilename = $this->generateUniqueFilename();

        // Take binary data, and save with or without a filepath
        if ($document->path) {
            Storage::put("{$document->path}/$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
        } else {
            Storage::put("$randomlyGenerateUniqueFilename.pdf", $generatedDocument);
        }

        return $randomlyGenerateUniqueFilename;
    }
}
