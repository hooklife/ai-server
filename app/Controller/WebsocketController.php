<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AiServiceRecord;
use App\Services\OpenaiService;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Redis\Redis;
use Hyperf\WebSocketServer\Context;
use Swoole\Http\Request;

class WebsocketController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function onMessage($server, $frame): void
    {
        ['msg' => $message, 'act' => $action] = json_decode($frame->data, true);

        $token = Context::get('token');
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        if (!$user = $redis->get("token:" . $token)) {
            $server->push($frame->fd, json_encode([
                'action'  => 'no_auth',
                'message' => '权限认证失败,请重试'
            ]));
            return;
        }

        if ($action == 'jiemeng') {
            if (Context::get('action') == 'jiemeng') {
                $server->push($frame->fd, json_encode([
                    'action'  => 'one',
                    'message' => '同时只能进行一个任务'
                ]));
                return;
            }
            Context::set('action', 'jiemeng');
            $aiServiceRecord = AiServiceRecord::create([
                'question'    => $message,
                'template_id' => 1,
            ]);

            $openai = new OpenaiService();
            $answers = $openai->ask($message);
            $answerText = '';
            foreach ($answers as $answer) {
                $answerText .= $answer['answer'];
                $server->push($frame->fd, json_encode([
                    'act'     => 'answer',
                    'message' => $answer['answer']
                ]));
            }
            $aiServiceRecord->update(['user_id' => $user['id'], 'content' => $answerText]);
            $server->push($frame->fd, json_encode([
                'act'     => 'answer_finish',
                'message' => '回答结束'
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
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        $accessToken = $request->header['authorization'] ?? null;
        if (!$accessToken || !$redis->get("token:" . $accessToken)) {
            $server->close($request->fd);
            return;
        }
        Context::set('token', $accessToken);
    }
}