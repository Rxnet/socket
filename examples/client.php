<?php
require_once '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
\Rx\Scheduler::setDefaultFactory(function () use ($loop) {
    return new \Rx\Scheduler\EventLoopScheduler($loop);
});

$connector = new \Rxnet\Socket\Connector($loop);

$connector->connect('www.google.fr:80')
    ->timeout(100)
    ->subscribe(new \Rx\Observer\CallbackObserver(
            function (\Rxnet\Socket\Connection $connection) use ($loop) {
                echo "connected to {$connection->getRemoteAddress()} \n";
                $connection
                    ->subscribe(new \Rx\Observer\CallbackObserver(
                        function ($data) {
                            echo '.';
                            //var_dump($data);
                        },
                        function ($e) {
                            echo $e->getMessage();
                        },
                        function () {
                            echo 'completed';
                        }
                    ));
                $loop->addTimer(.1, function () use ($connection) {
                    $connection->write("GET /?gfe_rd=cr&dcr=0&ei=YWhsWsTDIZOm8wep_beACA HTTP/1.0\r\nHost: www.google.fr\r\n\r\n");
                });

            })
    );

$loop->run();