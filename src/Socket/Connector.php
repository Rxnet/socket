<?php

namespace Rxnet\Socket;

use EventLoop\EventLoop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Rx\Observable;

class Connector
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(LoopInterface $loop = null)
    {
        $this->loop = $loop ? : EventLoop::getLoop();
    }

    public function connect($dsn, $options = []): Observable
    {
        $connector = new \React\Socket\Connector($this->loop, $options);
        return Observable::fromPromise($connector->connect($dsn))
            ->map(function (ConnectionInterface $server) {
                return new Connection($server);
            });
    }
}