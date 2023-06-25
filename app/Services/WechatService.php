<?php

namespace App\Services;

use EasyWeChat\MiniApp\Application;
use Hyperf\Context\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

class WechatService
{
    protected Application $app;

    public function __construct()
    {
        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $app = new Application(config('easywechat'));
        $app->setCache($cache);
        $this->app = $app;
    }

    public function login(string $code): array
    {
        return $this->app->getUtils()->codeToSession($code);
    }



}