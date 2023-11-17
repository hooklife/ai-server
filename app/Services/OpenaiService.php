<?php

namespace App\Services;

use HaoZiTeam\ChatGPT\V2 as ChatGPTV2;

use function Hyperf\Config\config;

class OpenaiService
{
    protected ChatGPTV2 $chatGPT;

    public function __construct()
    {
        $servers = config('openai.servers');
        $config = $servers[config('openai.default')];
        $this->chatGPT = new ChatGPTV2($config['secret_key'], $config['endpoint']);
    }

    public function ask($template,$message)
    {
        $this->chatGPT->addMessage($template['prompts'],'system');
        $message = str_replace($template['template'],"#content#",$message);
        return $this->chatGPT->ask($message, stream: true);
    }


}