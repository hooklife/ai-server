<?php

namespace App\Services;

use OSS\OssClient;

use function Hyperf\Support\env;

class OssService
{
    public OssClient $client;
    public function __construct()
    {
        $this->client = new OssClient(
            env('OSS_ACCESS_KEY'),
            env('OSS_ACCESS_SECRET'),
            env('OSS_ENDPOINT')
        );
    }
}
