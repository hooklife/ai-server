<?php
return [
    'apps'=> [
        'jiemeng'=>[
            'app_id'  => 'wx75d2d05eccc42932',
            'secret'  => '30f46b68f1cbc4871213b293959a8c32',
        ],
        'xiaohongshu_wenan'=>[
            'app_id'    =>'wx74111e6ccde16c5c',
            'secret'    =>'b86a1d7f646d3af48366cb641c16f54f'
        ],
        'chongwu_jiedu'=> [
            'app_id' => 'wx7c22e8e3ca591a17',
            'secret' => '47474dab0903f6c08811fde448f2d65d'
        ]
    ],
    'base'=> [
        'http'    => [
            'throw'   => true, // 状态码非 200、300 时是否抛出异常，默认为开启
            'timeout' => 5.0,
            'retry'   => true,
        ],
    ]
   
];