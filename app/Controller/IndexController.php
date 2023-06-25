<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Model\User;
use App\Services\WechatService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

class IndexController extends AbstractController
{
    #[Inject]
    protected WechatService $wechatService;

    #[Inject]
    protected Redis $redis;


    public function login()
    {
        $code = $this->request->input('code', '1');
        $loginResult = $this->wechatService->login($code);

        $user = User::firstOrCreate(['openid' => $loginResult['openid']]);
        $accessToken = password_hash((string)$user->id, PASSWORD_BCRYPT);
        $this->redis->setex("token:" . $accessToken, 3600 * 2, $user->toArray());

        return $this->response->json(['access_token' => $accessToken, 'expire' => 3600 * 2]);
    }
}
