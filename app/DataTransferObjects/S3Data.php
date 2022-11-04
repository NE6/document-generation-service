<?php

namespace App\DataTransferObjects;

class S3Data
{
    /**
     * Document object.
     *
     * @param string $key
     * @param string $secret
     * @param string $region
     * @param string $bucket
     */
    public function __construct(
        public readonly string $key,
        public readonly string $secret,
        public readonly string $region,
        public readonly string $bucket
    ){}
}
