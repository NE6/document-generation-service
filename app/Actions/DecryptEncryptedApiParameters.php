<?php

namespace App\Actions;

use App\DataTransferObjects\Document;

class DecryptEncryptedApiParameters
{
    /**
     * Will decrypt the values we expect to be encrypted.
     *
     * @param Document $document
     * @return Document
     */
    public static function execute(Document $document): Document
    {
        if ($document->aws_key)
        {
            $document->aws_key = openssl_decrypt($document->aws_key,
                config('app.aws_inbound_api_decryption_method'),
                config('app.aws_inbound_api_decryption_key'));
        }

        if ($document->aws_secret)
        {
            $document->aws_secret = openssl_decrypt($document->aws_secret,
                config('app.aws_inbound_api_decryption_method'),
                config('app.aws_inbound_api_decryption_key'));
        }

        if ($document->aws_bucket)
        {
            $document->aws_bucket = openssl_decrypt($document->aws_bucket,
                config('app.aws_inbound_api_decryption_method'),
                config('app.aws_inbound_api_decryption_key'));
        }

        return $document;
    }
}
