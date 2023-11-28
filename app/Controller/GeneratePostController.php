<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\OssService;
use App\Services\OTSService;
use mikehaertl\wkhtmlto\Image;
use Hyperf\Di\Annotation\Inject;

use function Hyperf\Support\env;

class GeneratePostController extends AbstractController
{
    #[Inject]
    protected OTSService $otsService;

    #[Inject]
    protected OssService $ossService;

    public function index()
    {
        $recordId = $this->request->input('record_id');
        $record =  $this->otsService->getRecordById($recordId);

        $template = file_get_contents('storage/templates/jiemeng/index.html');
        $page = str_replace(['[question]','[content]'],[$record['question'],$record['content']],$template);
        $image = new Image([
            'width'=>'505',
            'quality'=> 70
        ]);
        $image->setPage($page);
        $image->setOptions([
            'enable-local-file-access',
        ]);

        // $this->ossService->client->putObject('ai-server-static',"poster/{$recordId}.jpg" , $image->toString());
        // return $this->response->json([
        //     'img'=>env('OSS_DOMAIN')."/poster/{$recordId}.jpg"
        // ]);
        return $this->response->raw($image->toString())->withHeader('Content-Type','image/jpeg');
    }

}
