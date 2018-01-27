<?php
require_once '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\Rx\Scheduler::setDefaultFactory(function () use ($loop) {
    return new \Rx\Scheduler\EventLoopScheduler($loop);
});

$server = new \Rxnet\Socket\Server($loop);

$server->listen('0.0.0.0:9999')
    ->subscribe(function (\Rxnet\Socket\Connection $client) use ($loop) {
        echo "Received connection from {$client->getRemoteAddress()} \n";

        $connector = new \Rxnet\Socket\Connector($loop);
        $connector->connect('www.google.fr:80')
            ->subscribe(new \Rx\Observer\CallbackObserver(
                    function (\Rxnet\Socket\Connection $connection) use ($client) {
                        echo "connected to {$connection->getRemoteAddress()} \n";
                        echo "Stream all received data to {$client->getRemoteAddress()} \n ";
                        $connection->subscribe($client);

                        $connection->write("GET /?gfe_rd=cr&dcr=0&ei=YWhsWsTDIZOm8wep_beACA HTTP/1.0\r\nHost: www.google.fr\r\n\r\n");
                    })
            );
    });

echo "Server listening on port 9999 \n";

$loop->run();