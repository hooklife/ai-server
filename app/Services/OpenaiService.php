<?php

namespace App\Services;

use HaoZiTeam\ChatGPT\V2 as ChatGPTV2;

class OpenaiService
{
    protected ChatGPTV2 $chatGPT;

    public function __construct()
    {
        $servers = config('openai.servers');
        $config = $servers[config('openai.default')];
        $this->chatGPT = new ChatGPTV2($config['secret_key'], $config['endpoint']);
    }

    public function ask($message = '')
    {
        $template = '我请求你担任中国传统的周公解梦师的角色。我将会给你我的梦境，请你解释我的梦境，并为其提供相应的指导和建议。';
        $this->chatGPT->addMessage($template, 'system');
        return $this->chatGPT->ask($message, stream: true);
    }


}