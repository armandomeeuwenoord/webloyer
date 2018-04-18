<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApplySettings;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Ngmy\Webloyer\IdentityAccess\Domain\Model\User\User;
use Ngmy\Webloyer\Webloyer\Application\Server\ServerService;
use Ngmy\Webloyer\Webloyer\Domain\Model\Server\Server;
use Ngmy\Webloyer\Webloyer\Domain\Model\Server\ServerId;
use Ngmy\Webloyer\Webloyer\Port\Adapter\Form\ServerForm\ServerForm;
use Session;
use Tests\Helpers\ControllerTestHelper;
use Tests\Helpers\DummyMiddleware;
use Tests\Helpers\MockeryHelper;
use TestCase;

class ServersControllerTest extends TestCase
{
    use ControllerTestHelper;

    use MockeryHelper;

    private $serverForm;

    private $serverService;

    public function setUp()
    {
        parent::setUp();

        $this->app->instance(ApplySettings::class, new DummyMiddleware());

        Session::start();

        $user = $this->mock(User::class);
        $user->shouldReceive('can')->andReturn(true);
        $user->shouldReceive('name');
        $this->auth($user);

        $this->serverForm = $this->mock(ServerForm::class);
        $this->serverService = $this->mock(ServerService::class);

        $this->app->instance(ServerForm::class, $this->serverForm);
        $this->app->instance(ServerService::class, $this->serverService);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->closeMock();
    }

    public function test_Should_DisplayIndexPage_When_IndexPageIsRequested()
    {
        $server = $this->createServer();
        $servers = new Collection([
            $server,
        ]);
        $page = 1;
        $perPage = 10;

        $this->serverService
            ->shouldReceive('getServersByPage')
            ->with($page, $perPage)
            ->andReturn(
                new LengthAwarePaginator(
                    $servers,
                    $servers->count(),
                    $perPage,
                    $page,
                    [
                        'path' => Paginator::resolveCurrentPath(),
                    ]
                )
            )
            ->once();

        $this->get('servers');

        $this->assertResponseOk();
        $this->assertViewHas('servers');
    }

    public function test_Should_DisplayCreatePage_When_CreatePageIsRequested()
    {
        $this->get('servers/create');

        $this->assertResponseOk();
    }

    public function test_Should_RedirectToIndexPage_When_StoreProcessSucceeds()
    {
        $this->serverForm
            ->shouldReceive('save')
            ->andReturn(true)
            ->once();

        $this->post('servers');

        $this->assertRedirectedToRoute('servers.index');
    }

    public function test_Should_RedirectToCreatePage_When_StoreProcessFails()
    {
        $this->serverForm
            ->shouldReceive('save')
            ->andReturn(false)
            ->once();

        $this->serverForm
            ->shouldReceive('errors')
            ->withNoArgs()
            ->andReturn(new MessageBag())
            ->once();

        $this->post('servers');

        $this->assertRedirectedToRoute('servers.create');
        $this->assertSessionHasErrors();
    }

    public function test_Should_DisplayShowPage_When_ShowPageIsRequestedAndResourceIsFound()
    {
        $server = $this->createServer();

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($server->serverId()->id())
            ->andReturn($server)
            ->once();

        $this->get("servers/{$server->serverId()->id()}");

        $this->assertResponseOk();
        $this->assertViewHas('server');
    }

    public function test_Should_DisplayNotFoundPage_When_ShowPageIsRequestedAndResourceIsNotFound()
    {
        $serverId = 1;

        $this->serverService
            ->shouldReceive('getServerById')
            ->once()
            ->andReturn(null);

        $this->get("servers/$serverId");

        $this->assertResponseStatus(404);
    }

    public function test_Should_DisplayEditPage_When_EditPageIsRequestedAndResourceIsFound()
    {
        $server = $this->createServer();

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($server->serverId()->id())
            ->andReturn($server)
            ->once();

        $this->get("servers/{$server->serverId()->id()}/edit");

        $this->assertResponseOk();
        $this->assertViewHas('server');
    }

    public function test_Should_DisplayNotFoundPage_When_EditPageIsRequestedAndResourceIsNotFound()
    {
        $serverId = 1;

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($serverId)
            ->andReturn(null)
            ->once();

        $this->get("servers/$serverId/edit");

        $this->assertResponseStatus(404);
    }

    public function test_Should_RedirectToIndexPage_When_UpdateProcessSucceeds()
    {
        $server = $this->createServer();

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($server->serverId()->id())
            ->andReturn($server)
            ->once();

        $this->serverForm
            ->shouldReceive('update')
            ->andReturn(true)
            ->once();

        $this->put("servers/{$server->serverId()->id()}");

        $this->assertRedirectedToRoute('servers.index');
    }

    public function test_Should_RedirectToEditPage_When_UpdateProcessFails()
    {
        $server = $this->createServer();

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($server->serverId()->id())
            ->andReturn($server)
            ->once();

        $this->serverForm
            ->shouldReceive('update')
            ->andReturn(false)
            ->once();

        $this->serverForm
            ->shouldReceive('errors')
            ->withNoArgs()
            ->andReturn(new MessageBag())
            ->once();

        $this->put("servers/{$server->serverId()->id()}");

        $this->assertRedirectedToRoute('servers.edit', [$server->serverId()->id()]);
        $this->assertSessionHasErrors();
    }

    public function test_Should_DisplayNotFoundPage_When_UpdateProcessIsRequestedAndResourceIsNotFound()
    {
        $serverId = 1;

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($serverId)
            ->andReturn(null)
            ->once();

        $this->put("servers/$serverId");

        $this->assertResponseStatus(404);
    }

    public function test_Should_RedirectToIndexPage_When_DestroyProcessIsRequestedAndDestroyProcessSucceeds()
    {
        $server = $this->createServer();

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($server->serverId()->id())
            ->andReturn($server)
            ->once();

        $this->serverService
            ->shouldReceive('removeServer')
            ->once();

        $this->delete("servers/{$server->serverId()->id()}");

        $this->assertRedirectedToRoute('servers.index');
    }

    public function test_Should_DisplayNotFoundPage_When_DestroyProcessIsRequestedAndResourceIsNotFound()
    {
        $serverId = 1;

        $this->serverService
            ->shouldReceive('getServerById')
            ->with($serverId)
            ->andReturn(null)
            ->once();

        $this->delete("servers/$serverId");

        $this->assertResponseStatus(404);
    }

    private function createServer(array $params = [])
    {
        $serverId = 1;
        $name = '';
        $description = '';
        $body = '';
        $createdAt = null;
        $updatedAt = null;
        $concurrencyVersion = '';

        extract($params);

        $server = $this->mock(Server::class);

        $server->shouldReceive('serverId')->andReturn(new ServerId($serverId));
        $server->shouldReceive('name')->andReturn($name);
        $server->shouldReceive('description')->andReturn($description);
        $server->shouldReceive('body')->andReturn($body);
        $server->shouldReceive('createdAt')->andReturn(new Carbon($createdAt));
        $server->shouldReceive('updatedAt')->andReturn(new Carbon($updatedAt));
        $server->shouldReceive('concurrencyVersion')->andReturn($concurrencyVersion);

        return $server;
    }
}
