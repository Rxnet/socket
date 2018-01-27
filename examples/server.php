<?php
require_once '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

\Rx\Scheduler::setDefaultFactory(function() use($loop){
    return new \Rx\Scheduler\EventLoopScheduler($loop);
});

$server = new \Rxnet\Socket\Server($loop);

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

$loop->run();