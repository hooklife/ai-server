<?php

    return [
        'default' => 'oh_my_gpt',
        'servers' => [
            "oh_my_gpt"=>[
                    'endpoint'=>'https://aigptx.top/',
                    'secret_key'=>'sk-AJLy0PaT70000f83bF4bT3BlBKFJ69cB880A57c749689D69'
            ],
            "qcloud_proxy" => [
                'endpoint'   => 'http://43.153.69.253:8080/',
                'secret_key' => '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'
            ],
            "openai-sb" => [
                'endpoint'   => 'https://api.openai-sb.com/',
                'secret_key' => 'sb-1fd0ac7fc4cce49ee710215aad2bd87cdaacc937778939d6'
            ]
        ]
    ];