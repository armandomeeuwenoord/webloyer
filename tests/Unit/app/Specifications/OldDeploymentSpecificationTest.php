<?php

namespace Tests\Unit\app\Specifications;

use App\Models\Deployment;
use App\Models\Project;
use App\Specifications\OldDeploymentSpecification;
use DateTime;
use Tests\TestCase;

class OldDeploymentSpecificationTest extends TestCase
{
    protected $mockProjectModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockProjectModel = $this->partialMock(Project::class);
    }

    public function testShouldGetSatisfyingElementsWhenDaysToKeepDeploymentsIsSetAndKeepLastDeploymentIsFalseAndMaxNumberDeploymentsToKeepIsSet()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployment1 = $this->partialMock(Deployment::class);
        $deployment2 = $this->partialMock(Deployment::class);
        $deployment3 = $this->partialMock(Deployment::class);
        $deployment4 = $this->partialMock(Deployment::class);
        $deployment1->number = 1;
        $deployment2->number = 2;
        $deployment3->number = 3;
        $deployment4->number = 4;

        $deployments = collect([
            $deployment4,
            $deployment3,
            $deployment2,
            $deployment1,
        ]);

        $pastDaysToKeepDeployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $pastNumToKeepDeployments = collect([
            $deployment3,
            $deployment2,
            $deployment1,
        ]);

        $this->mockProjectModel->days_to_keep_deployments = 1;
        $this->mockProjectModel->keep_last_deployment = 0;
        $this->mockProjectModel->max_number_of_deployments_to_keep = 1;

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments)
            ->shouldReceive('getDeploymentsWhereCreatedAtBefore')
            ->once()
            ->andReturn($pastDaysToKeepDeployments)
            ->shouldReceive('getDeploymentsWhereNumberBefore')
            ->once()
            ->andReturn($pastNumToKeepDeployments)
            ->shouldReceive('getLastDeployment')
            ->once()
            ->andReturn($deployment4);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEquals($deployment3, $oldDeployments[0]);
        $this->assertEquals($deployment2, $oldDeployments[1]);
        $this->assertEquals($deployment1, $oldDeployments[2]);
    }

    public function testShouldGetSatisfyingElementsWhenDaysToKeepDeploymentsIsNotSetAndKeepLastDeploymentIsFalseAndMaxNumberDeploymentsToKeepIsSet()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployment1 = $this->partialMock(Deployment::class);
        $deployment2 = $this->partialMock(Deployment::class);
        $deployment3 = $this->partialMock(Deployment::class);
        $deployment4 = $this->partialMock(Deployment::class);
        $deployment1->number = 1;
        $deployment2->number = 2;
        $deployment3->number = 3;
        $deployment4->number = 4;

        $deployments = collect([
            $deployment4,
            $deployment3,
            $deployment2,
            $deployment1,
        ]);

        $pastNumToKeepDeployments = collect([
            $deployment3,
            $deployment2,
            $deployment1,
        ]);

        $this->mockProjectModel->days_to_keep_deployments = null;
        $this->mockProjectModel->keep_last_deployment = 0;
        $this->mockProjectModel->max_number_of_deployments_to_keep = 1;

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments)
            ->shouldReceive('getDeploymentsWhereNumberBefore')
            ->once()
            ->andReturn($pastNumToKeepDeployments)
            ->shouldReceive('getLastDeployment')
            ->once()
            ->andReturn($deployment4);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEquals($deployment3, $oldDeployments[0]);
        $this->assertEquals($deployment2, $oldDeployments[1]);
        $this->assertEquals($deployment1, $oldDeployments[2]);
    }

    public function testShouldGetSatisfyingElementsWhenDaysToKeepDeploymentsIsSetAndKeepLastDeploymentIsFalseAndMaxNumberDeploymentsToKeepIsNotSet()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployment1 = $this->partialMock(Deployment::class);
        $deployment2 = $this->partialMock(Deployment::class);
        $deployment1->number = 1;
        $deployment2->number = 2;

        $deployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $pastDaysToKeepDeployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $this->mockProjectModel->days_to_keep_deployments = 1;
        $this->mockProjectModel->keep_last_deployment = 0;
        $this->mockProjectModel->max_number_of_deployments_to_keep = null;

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments)
            ->shouldReceive('getDeploymentsWhereCreatedAtBefore')
            ->once()
            ->andReturn($pastDaysToKeepDeployments);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEquals($deployment2, $oldDeployments[0]);
        $this->assertEquals($deployment1, $oldDeployments[1]);
    }

    public function testShouldGetSatisfyingElementsWhenDaysToKeepDeploymentsIsSetAndKeepLastDeploymentIsTrueMaxNumberDeploymentsToKeepIsNotSet()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployment1 = $this->partialMock(Deployment::class);
        $deployment2 = $this->partialMock(Deployment::class);
        $deployment1->number = 1;
        $deployment2->number = 2;

        $deployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $pastDaysToKeepDeployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $this->mockProjectModel->days_to_keep_deployments = 1;
        $this->mockProjectModel->keep_last_deployment = 1;
        $this->mockProjectModel->max_number_of_deployments_to_keep = null;

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments)
            ->shouldReceive('getDeploymentsWhereCreatedAtBefore')
            ->once()
            ->andReturn($pastDaysToKeepDeployments)
            ->shouldReceive('getLastDeployment')
            ->once()
            ->andReturn($deployment2);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEquals($deployment1, $oldDeployments[0]);
    }

    public function testShouldGetSatisfyingElementsWhenDaysToKeepDeploymentsIsNotSetAndKeepLastDeploymentIsFalseAndMaxNumberDeploymentsToKeepIsNotSet()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployment1 = $this->partialMock(Deployment::class);
        $deployment2 = $this->partialMock(Deployment::class);
        $deployment1->number = 1;
        $deployment2->number = 2;

        $deployments = collect([
            $deployment2,
            $deployment1,
        ]);

        $this->mockProjectModel->days_to_keep_deployments = null;
        $this->mockProjectModel->keep_last_deployment = 0;
        $this->mockProjectModel->max_number_of_deployments_to_keep = null;

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEmpty($oldDeployments);
    }

    public function testShouldGetSatisfyingElementsWhenDeploymentsDoNotExists()
    {
        $date = new DateTime();
        $spec = new OldDeploymentSpecification($date);

        $deployments = collect([]);

        $this->mockProjectModel
            ->shouldReceive('getDeployments')
            ->once()
            ->andReturn($deployments);

        $oldDeployments = $spec->satisfyingElementsFrom($this->mockProjectModel);

        $this->assertEmpty($oldDeployments);
    }
}
