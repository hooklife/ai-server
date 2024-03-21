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
use App\Services\OTSService;
use App\Services\WechatService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;

class IndexController extends AbstractController
{
    #[Inject]
    protected WechatService $wechatService;
    #[Inject]
    protected OTSService $otsService;



    public function login()
    {
        $code = $this->request->input('code', '1');
        $app = $this->request->input('app','jiemeng');
        $loginResult = $this->wechatService->login($code,$app);

        $user = $this->otsService->getUser($loginResult['openid']);
        if(!$user['primary_key']){
            $this->otsService->createUser($loginResult['openid']);
        }
        $accessToken = password_hash($loginResult['openid'], PASSWORD_BCRYPT);
        $this->otsService->setToken($accessToken,$loginResult['openid']);

        return $this->response->json(['access_token' => $accessToken, 'expire' => 3600 * 2]);
    }
}
