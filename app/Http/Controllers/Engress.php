<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\Document as DocumentDataTransferObject;
use App\Http\Requests\Document;

class Engress extends Controller
{
    public function consumeDocumentRequest(Document $request)
    {
        $document = new DocumentDataTransferObject(...$request->all());
        dd($document);
    }
}
