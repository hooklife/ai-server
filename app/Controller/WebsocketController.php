<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AiServiceRecord;
use App\Services\OpenaiService;
use App\Services\OTSService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Hyperf\WebSocketServer\Context;
use Swoole\Http\Request;

class WebsocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    #[Inject]
    protected OTSService $otsService;

    public function onMessage($server, $frame): void
    {
        [ 'act' => $action ,'payload'=> $payload] = json_decode($frame->data, true);

        $token = Context::get('token');
        if (!$token || !$userOpenid = $this->otsService->getToken($token)) {
            $server->push($frame->fd, json_encode([
                'action'  => 'no_auth',
                'message' => '权限认证失败,请重试'
            ]));
            return;
        }
        if ($action == 'start_generate') {
            if (Context::get('action') == 'jiemeng') {
                $server->push($frame->fd, json_encode([
                    'action'  => 'one',
                    'message' => '同时只能进行一个任务'
                ]));
                return;
            }
            Context::set('action', 'jiemeng');

            $template = $this->otsService->getTempalteByName($payload['template_name']);
            $recordId = $this->otsService->createRecord($payload['message'],$userOpenid ??1);
            $openai = new OpenaiService();
            $answers = $openai->ask($template,$payload['message']);
            $answerText = '';
            foreach ($answers as $answer) {
                $answerText .= $answer['answer'];
                $server->push($frame->fd, json_encode([
                    'act'     => 'answer',
                    'message' => $answer['answer']
                ]));
            }
            $this->otsService->updateRecord($recordId,$answerText);
            $server->push($frame->fd, json_encode([
                'act'     => 'answer_finish',
                'message' => '回答结束',
                'payload' => [
                    'record_id'=> $recordId
                ]
            ]));


            Context::destroy('action');
            return;
        }
        $server->push($frame->fd, json_encode([
            'act'     => 'not_found',
            'message' => '未匹配路由'
        ]));
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
    }

    public function onOpen($server, $request): void
    {
        $accessToken = $request->header['authorization'] ?? null;
        if (!$accessToken || !$this->otsService->getToken($accessToken)) {
            $server->close($request->fd);
            return;
        }
        
        Context::set('token', $accessToken);
    }
}