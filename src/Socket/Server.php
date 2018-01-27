<?php

namespace Rxnet\Socket;

use EventLoop\EventLoop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Rx\Disposable\CallbackDisposable;
use Rx\Observable;
use Rx\ObserverInterface;

class Server
{
    /**
     * @var LoopInterface
     */
    protected $loop;


    public function __construct(LoopInterface $loop = null)
    {
        $this->loop = $loop ?: EventLoop::getLoop();
    }

    public function listen($dsn, $options = []): Observable
    {
        $server = new \React\Socket\Server($dsn, $this->loop, $options);

        return Observable::create(function (ObserverInterface $observer) use ($server) {
            $server->on('connection', function (ConnectionInterface $client) use ($observer) {
                $observer->onNext(new Connection($client));
            });
            $server->on('error', [$observer, 'onError']);

            return new CallbackDisposable(function () use ($server) {
                $server->close();
            });
        });
    }
}