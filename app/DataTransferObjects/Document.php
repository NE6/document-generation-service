<?php

namespace App\DataTransferObjects;

class Document
{
    /**
     * Document object.
     *
     * @param int $id
     * @param string $name
     * @param string $contents
     * @param string|null $path
     * @param string $bucket
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $contents,
        public readonly ?string $path,
        public readonly string $bucket
    ){}
}
