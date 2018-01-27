<?php
require_once '../vendor/autoload.php';

\Rx\Scheduler::setDefaultFactory(function() {
    return new \Rx\Scheduler\EventLoopScheduler(EventLoop\getLoop());
});

$server = new \Rxnet\Socket\Server();

$server->listen('0.0.0.0:9999')
    ->subscribe(function(\Rxnet\Socket\Connection $connection) {
        $connection->subscribe(function($data) use($connection) {
            echo 'received '.$data." from ".$connection->getRemoteAddress()." \n";
            $connection->end('ok');
        }, null, function() {
            echo "end connection\n";
        });
        $connection->write('coucou');
    });

echo "Server listening on port 9999 \n";