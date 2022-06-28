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
     */
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $contents,
    ){}
}
