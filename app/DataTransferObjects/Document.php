<?php

namespace App\DataTransferObjects;

class Document
{
    /**
     * @param  int  $id
     * @param  string  $name
     * @param  string  $contents
     * @param  string|null  $path
     * @param  string|null  $key
     * @param  string|null  $secret
     * @param  string|null  $region
     * @param  string|null  $bucket
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $contents,
        public readonly ?string $path = null,
        public readonly ?string $key = null,
        public readonly ?string $secret = null,
        public readonly ?string $region = null,
        public readonly ?string $bucket = null
    ) {
    }
}
