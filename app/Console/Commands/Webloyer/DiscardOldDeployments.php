<?php

namespace App\Console\Commands\Webloyer;

use Illuminate\Console\Command;
use Webloyer\App\Service\Deployment\DeleteOldDeploymentsRequest;
use Webloyer\App\Service\Deployment\DeleteOldDeploymentsService;

class DiscardOldDeployments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webloyer:discard-old-deployments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discard old deployments';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(DeleteOldDeploymentsService $service)
    {
        $request = (new DeleteOldDeploymentsRequest())->setDateTime('now');
        $service->execute($request);
    }
}
