<?php
require_once '../vendor/autoload.php';

\Rx\Scheduler::setDefaultFactory(function () {
    return new \Rx\Scheduler\EventLoopScheduler(EventLoop\getLoop());
});

$connector = new \Rxnet\Socket\Connector();

$connector->connect('www.google.fr:80')
    ->timeout(100)
    ->subscribe(new \Rx\Observer\CallbackObserver(
            function (\Rxnet\Socket\Connection $connection) {
                echo "connected to {$connection->getRemoteAddress()} \n";
                $connection
                    ->subscribe(
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
                    );
                \EventLoop\addTimer(.1, function () use ($connection) {
                    $connection->write("GET /?gfe_rd=cr&dcr=0&ei=YWhsWsTDIZOm8wep_beACA HTTP/1.0\r\nHost: www.google.fr\r\n\r\n");
                });

            })
    );