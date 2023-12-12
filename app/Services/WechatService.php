<?php

namespace App\Services;

use EasyWeChat\MiniApp\Application;
use Hyperf\Context\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

use function Hyperf\Config\config;

class WechatService
{
    protected $apps = [];

    public function __construct()
    {
        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $easywechat = config('easywechat');
        foreach($easywechat['apps'] as $appName => $app){
            $this->apps[$appName] = new Application(array_merge(
                $easywechat['base'],
                $app
            ));
            $this->apps[$appName]->setCache($cache);
        }
        
    }

    public function login(string $code,$appName = 'jiemeng'): array
    {
        return $this->apps[$appName]->getUtils()->codeToSession($code);
    }



}