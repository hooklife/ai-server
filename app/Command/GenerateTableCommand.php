<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\OTSService;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;

#[Command]
class GenerateTableCommand extends HyperfCommand
{
    #[Inject]
    protected OTSService $otsService;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen:table');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
        $this->otsService->updateRecord("梦到了狗");
        $this->line('Hello Hyperf!', 'info');
    }
}
