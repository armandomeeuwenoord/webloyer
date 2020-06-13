<?php

declare(strict_types=1);

namespace Webloyer\App\Service\Project;

use Webloyer\Domain\Model\Project\ProjectId;

class UpdateProjectService extends ProjectService
{
    /**
     * @param UpdateProjectRequest $request
     * @return void
     */
    public function execute($request = null)
    {
        assert(!is_null($request));
        $id = new ProjectId($request->getId());
        $project = $this->getNonNullProject($id)
            ->changeName($request->getName())
            ->changeRecipes(...$request->getRecipeIds())
            ->changeServer($request->getServerId())
            ->changeRepositoryUrl($request->getRepositoryUrl())
            ->changeStageName($request->getStageName())
            ->changeDeployPath($request->getDeployPath())
            ->changeEmailNotificationRecipient($request->getEmailNotificationRecipient())
            ->changeDeploymentKeepDays($request->getDeploymentKeepDays())
            ->changeKeepLastDeployment($request->getKeepLastDeployment())
            ->changeDeploymentKeepMaxNumber($request->getDeploymentKeepMaxNumber())
            ->changeGitHubWebhookSecret($request->getGitHubWebhookSecret())
            ->changeGitHubWebhookExecutor($request->getGitHubWebhookExecutor());
        $this->projectRepository->save($project);
    }
}
