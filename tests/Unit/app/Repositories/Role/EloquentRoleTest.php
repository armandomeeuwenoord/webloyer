<?php

namespace Tests\Unit\app\Repositories\Role;

use App\Repositories\Role\EloquentRole;
use Kodeine\Acl\Models\Eloquent\Role;
use Tests\TestCase;

class EloquentRoleTest extends TestCase
{
    protected $useDatabase = true;

    public function testShouldGetRoleById()
    {
        $role = factory(Role::class)->create();

        $sut = $this->makeSut();

        $actual = $sut->byId($role->id);

        $this->assertTrue($role->is($actual));
    }

    public function testShouldGetRolesByPage()
    {
        $roles = factory(Role::class, 5)->create();

        $sut = $this->makeSut();

        $actual = $sut->byPage();

        $this->assertCount(5, $actual->items());
    }

    public function testShouldCreateNewRole()
    {
        $sut = $this->makeSut();

        $actual = $sut->create([
            'name'        => 'Role 1',
            'slug'        => 'role_1',
            'description' => '',
        ]);

        $this->assertDatabaseHas('roles', $actual->toArray());
    }

    public function testShouldUpdateExistingRole()
    {
        $role = factory(Role::class)->create();

        $sut = $this->makeSut();

        $sut->update([
            'id'          => $role->id,
            'name'        => 'Role 2',
            'slug'        => 'role_2',
            'description' => 'Role 2.',
        ]);

        $this->assertDatabaseHas('roles', [
            'id'          => $role->id,
            'name'        => 'Role 2',
            'slug'        => 'role_2',
            'description' => 'Role 2.',
        ]);
    }

    public function testShouldDeleteExistingRole()
    {
        $role = factory(Role::class)->create();

        $sut = $this->makeSut();

        $sut->delete($role->id);

        $this->assertDatabaseMissing('roles', [ 'id' => $role->id]);
    }

    public function makeSut(): EloquentRole
    {
        return new EloquentRole(new Role());
    }
}
