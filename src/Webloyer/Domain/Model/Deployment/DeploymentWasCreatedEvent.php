<?php

declare(strict_types=1);

namespace Webloyer\Domain\Model\Deployment;

use Common\Domain\Model\Event\{
    DomainEvent,
    PublishableDomainEvent,
};
use Webloyer\Domain\Model\Project\ProjectId;

class DeploymentWasCreatedEvent implements DomainEvent, PublishableDomainEvent
{
    /** @var ProjectId */
    private $projectId;
    /** @var DeploymentNumber */
    private $number;
    /** @var DeploymentTask */
    private $task;

    /**
     * @param ProjectId $projectId
     * @param DeploymentNumber $number
     * @param DeploymentTask $task
     * @return void
     */
    public function __construct(
        ProjectId $projectId,
        DeploymentNumber $number,
        DeploymentTask $task
    ) {
        $this->projectId = $projectId;
        $this->number = $number;
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function projectId(): string
    {
        return $this->projectId->value();
    }

    /**
     * @return int
     */
    public function number(): int
    {
        return $this->number->value();
    }

    /**
     * @return string
     */
    public function task(): string
    {
        return $this->task->value();
    }
}
