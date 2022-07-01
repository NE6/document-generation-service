<?php

namespace App\DataTransferObjects;

class Document
{
    /**
     * @param int $id
     * @param string $name
     * @param string $contents
     * @param string|null $path
     * @param string|null $aws_key
     * @param string|null $aws_secret
     * @param string|null $aws_region
     * @param string|null $aws_bucket
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $contents,
        public readonly ?string $path = null,
        public ?string $aws_key = null,
        public ?string $aws_secret = null,
        public ?string $aws_region = null,
        public ?string $aws_bucket = null
    ){}
}
