<?php

namespace Rxnet\Socket;

use React\Socket\ConnectionInterface;
use Rx\Disposable\CallbackDisposable;
use Rx\DisposableInterface;
use Rx\Observable;
use Rx\ObserverInterface;
use Rx\Subject\Subject;

class Connection extends Observable implements ObserverInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct( $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param mixed $data
     * @return bool
     */
    public function write($data)
    {
        return $this->connection->write($data);
    }

    /**
     * Close given connection
     */
    public function close()
    {
        $this->connection->close();
    }

    public function end($data = null)
    {
        $this->connection->end($data);
    }

    public function getRemoteAddress()
    {
        return $this->connection->getRemoteAddress();
    }

    public function getLocalAddress()
    {
        return $this->connection->getLocalAddress();
    }

    public function resume()
    {
        $this->connection->resume();
    }

    public function pause()
    {
        $this->connection->pause();
    }

    public function onNext($value)
    {
        $this->write($value);
        $observers = $this->observers;
        foreach ($observers as $observer) {
            $observer->onNext($value);
        }
        return parent::onNext($value);
    }

    /**
     * @param \Rx\ObserverInterface $observer
     * @return \Rx\DisposableInterface
     */
    protected function _subscribe(ObserverInterface $observer): DisposableInterface
    {
        $this->connection->on('error', [$observer, 'onError']);
        $this->connection->on('data', [$observer, 'onNext']);
        $this->connection->on('end', [$observer, 'onCompleted']);
        $this->connection->on('close', [$observer, 'onCompleted']);

        return new CallbackDisposable([$this, 'close']);
    }

    public function onCompleted()
    {
        // TODO: Implement onCompleted() method.
    }

    public function onError(\Throwable $error)
    {
        // TODO: Implement onError() method.
    }
}