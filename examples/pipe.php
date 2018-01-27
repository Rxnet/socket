<?php
require_once '../vendor/autoload.php';

\Rx\Scheduler::setDefaultFactory(function () {
    return new \Rx\Scheduler\EventLoopScheduler(\EventLoop\getLoop());
});

$server = new \Rxnet\Socket\Server();

$server->listen('0.0.0.0:9999')
    ->subscribe(function (\Rxnet\Socket\Connection $client) {
        echo "Received connection from {$client->getRemoteAddress()} \n";

        $connector = new \Rxnet\Socket\Connector();
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
