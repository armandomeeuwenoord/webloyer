<?php

declare(strict_types=1);

namespace Webloyer\App\Service\Server;

use Common\App\Service\ApplicationService;
use InvalidArgumentException;
use Webloyer\Domain\Model\Server\{
    Server,
    ServerId,
    ServerRepository,
};

abstract class ServerService implements ApplicationService
{
    /** @var ServerRepository */
    protected $serverRepository;

    /**
     * @param ServerRepository $serverRepository
     * @return void
     */
    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    /**
     * @param ServerId $id
     * @return Server
     * @throws InvalidArgumentException
     */
    protected function getNonNullServer(ServerId $id): Server
    {
        $server = $this->serverRepository->findById($id);
        if (is_null($server)) {
            throw new InvalidArgumentException(
                'Server does not exists.' . PHP_EOL .
                'Id: ' . $id->value()
            );
        }
        return $server;
    }
}
