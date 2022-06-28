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
    public function consumeDocumentRequest(Document $request)
    {
        $document = new DocumentDataTransferObject(...$request->all());
        $randomlyGeneratedFilename = Str::random();

        try {

            $generatedDocument = BrowsershotLambda::html($document->contents)->pdf();
            Storage::disk('s3')->put("$randomlyGeneratedFilename.pdf", $generatedDocument);

            return response()->json([
                'id' => $document->id,
                'name' => $document->name,
                'filename' => $randomlyGeneratedFilename
            ]);

        } catch (\Exception $e) {
            return response(500)->json([
                'error' => $e->getMessage()
            ]);
        }
    }
}
