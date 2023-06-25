<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\AiServiceRecord;


use Hyperf\Context\ApplicationContext;
use Hyperf\Redis\Redis;
use Imagick;
use ImagickPixel;

class GeneratePostController extends AbstractController
{
    public function index()
    {
        $recordId = $this->request->input('record_id');


        $accessToken = $request->header['authorization'] ?? null;
        $redis = ApplicationContext::getContainer()->get(Redis::class);
//        if (!$user = $redis->get("token:" . $accessToken)) {
//            return $this->response->json(['message' => '权限认证失败,请重试'])->withStatus(401);
//        }
        $user = ['id' => 1];

        $record = AiServiceRecord::findOrFail($recordId);
        if ($user['id'] && $user['id'] != $record->user_id) {
            return $this->response->json(['message' => '权限认证失败了,请重试'])->withStatus(401);
        }

        $proc = proc_open("/usr/local/bin/wkhtmltoimage --enable-local-file-access  --crop-w 505 - -", [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);


        fwrite($pipes[0], file_get_contents("/Users/hooklife/Codes/ai-server-2/node/index.html"));
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);


        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($proc);
        var_dump($exitCode, $stderr);


        do {
            $status = proc_get_status($process);
            if (!feof($pipes[2])) {
//                $this->errors[] = fgets($pipes[2]);
                var_dump(2, fgets($pipes[2]));
            }
        } while ($status['running']);
//
        $exitCode = $status['exitcode'];
        var_dump($status);
//        if ($exitCode === 0) {
        proc_close($process);
        return $this->response->raw($outputStream)->withHeader('Content-Type', 'image/png');
//        }

//
        return $response->raw('Hello Hyperf!');
    }

    public function index2()
    {
        $draw = new \ImagickDraw();

        $draw->setStrokeOpacity(1);
        $draw->setStrokeWidth(2);
        $draw->setFillOpacity(0.2);

        $draw->roundRectangle(0, 0, 100, 100, 10, 10);

//        $im->newPseudoImage(300, 300, "caption:" . "Put your text" );


//
//
//        $image->newPseudoImage(300, 300, "caption:" . "Put your text" );
//
        $im = new Imagick();
        $im2 = new Imagick('/Users/hooklife/Codes/ai-server-2/node/backgroud.jpg');

//        $background = new ImagickPixel('rgba(255, 0, 0, 0.2)');
//
//        $im->setBackgroundColor($background);
        $im->setFont("/Users/hooklife/Codes/ai-server-2/node/fonts/CangJiGaoDeGuoMiaoHei-CJgaodeguomh-2.ttf");
//        $im->setpointsize(72);

        $im->setBackgroundColor('rgba(225,225,225,0.2)');
//        $im->setGravity(Imagick::GRAVITY_CENTER);
        $im->newPseudoImage(300, 300, "caption:" . "吃那亲亲亲亲亲亲亲");
        $im->colorizeImage('#000000',.2,true);
        $im->setImageFormat("png");
        $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);

        $im2->drawImage($draw);

        $im2->compositeImage($im,
            Imagick::COMPOSITE_ATOP,
            10, 10);


        return $this->response->raw($im2->getImageBlob())->withHeader('Content-Type', 'image/png');
    }

    function utf8_wordwrap($str, $width = 75, $break = "\n")
    {
        $lines = array();

        while (!empty($str)) {
            // We got a line with a break in it somewhere before the end
            if (preg_match('%^(.{1,' . $width . '})(?:\s|$)%', $str, $matches)) {
                // Add this line to the output
                $lines[] = $matches[1];

                // Trim it off the input ready for the next go
                $str = substr($str, strlen($matches[0]));
            } // Just take the next $width characters
            else {
                $lines[] = substr($str, 0, $width);

                // Trim it off the input ready for the next go
                $str = substr($str, $width);
            }
        }

        return implode($break, $lines);
    }
}
