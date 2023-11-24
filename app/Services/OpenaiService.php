<?php

namespace App\Services;

use HaoZiTeam\ChatGPT\V2 as ChatGPTV2;

use function Hyperf\Config\config;

class OpenaiService
{
    
    protected array $config;

    public function __construct()
    {
        $servers = config('openai.servers');
        $config = $servers[config('openai.default')];
    }

    public function ask($template,$message)
    {
        $chatGPT  = new ChatGPTV2($this->config['secret_key'], $this->config['endpoint'],timeout:15);
        $chatGPT->addMessage($template['prompts'],'system');
        $message = str_replace($template['template'],"#content#",$message);
        return $chatGPT->ask($message, stream: true);
    }


}