<?php

namespace Tests\Unit\app\Services\Form\Project;

use App\Models\Project;
use App\Repositories\Project\ProjectInterface;
use App\Services\Form\Project\ProjectForm;
use App\Services\Validation\ValidableInterface;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

class ProjectFormTest extends TestCase
{
    protected $mockValidator;

    protected $mockProjectRepository;

    protected $mockProjectModel;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockValidator = $this->mock(ValidableInterface::class);
        $this->mockProjectRepository = $this->mock(ProjectInterface::class);
        $this->mockProjectModel = $this->partialMock(Project::class);
    }

    public function testShouldSucceedToSaveAndNotAddProjectAttributeWhenValidationPassesAndDeployPathFieldIsNotSpecified()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(true);

        $project = $this->mockProjectModel
            ->shouldReceive('addMaxDeployment')
            ->once()
            ->shouldReceive('syncRecipes')
            ->once()
            ->mock();
        $this->mockProjectRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($project);

        $input = [
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->save($input);

        $this->assertTrue($result, 'Expected save to succeed.');
    }

    public function testShouldSucceedToSaveAndAddProjectAttributeWhenValidationPassesAndDeployPathFieldIsSpecified()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(true);

        $project = $this->mockProjectModel
            ->shouldReceive('addMaxDeployment')
            ->once()
            ->shouldReceive('syncRecipes')
            ->once()
            ->mock();
        $this->mockProjectRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($project);

        $input = [
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '/home/www',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->save($input);

        $this->assertTrue($result, 'Expected save to succeed.');
    }

    public function testShouldFailToSaveWhenValidationFails()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(false);

        $input = [
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->save($input);

        $this->assertFalse($result, 'Expected save to fail.');
    }

    public function testShouldSucceedToUpdateAndNotAddProjectAttributeWhenValidationPassesAndDeployPathFieldIsNotSpecified()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(true);

        $project = $this->mockProjectModel
            ->shouldReceive('syncRecipes')
            ->once()
            ->mock();
        $this->mockProjectRepository
            ->shouldReceive('byId')
            ->once()
            ->andReturn($project);
        $this->mockProjectRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $input = [
            'id'              => $project->id,
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->update($input);

        $this->assertTrue($result, 'Expected update to succeed.');
    }

    public function testShouldSucceedToUpdateAndAddProjectAttributeWhenValidationPassesAndDeployPathFieldIsSpecified()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(true);

        $project = $this->mockProjectModel
            ->shouldReceive('syncRecipes')
            ->once()
            ->mock();
        $this->mockProjectRepository
            ->shouldReceive('byId')
            ->once()
            ->andReturn($project);
        $this->mockProjectRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $input = [
            'id'              => $project->id,
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '/home/www',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->update($input);

        $this->assertTrue($result, 'Expected update to succeed.');
    }

    public function testShouldFailToUpdateWhenValidationFails()
    {
        $this->mockValidator
            ->shouldReceive('with')
            ->once()
            ->andReturn($this->mockValidator);
        $this->mockValidator
            ->shouldReceive('passes')
            ->once()
            ->andReturn(false);

        $input = [
            'recipe_id_order' => '3,1,2',
            'deploy_path'     => '',
        ];

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->update($input);

        $this->assertFalse($result, 'Expected update to fail.');
    }

    public function testShouldGetValidationErrors()
    {
        $this->mockValidator
            ->shouldReceive('errors')
            ->once()
            ->andReturn(new MessageBag());

        $form = new ProjectForm($this->mockValidator, $this->mockProjectRepository);
        $result = $form->errors();

        $this->assertEmpty($result);
    }
}
