# Socket observable
Ultra thin ReactPHP socket adapter to bring ReactiveX flavour.

## Client
Low level socket that connect to remote or throw an exception  
Then echo the received data has they arrive

```php
<?php
$connector = new \Rxnet\Socket\Connector($loop);
$options = [
    // See http://php.net/manual/en/context.socket.php for all tcp options
    'tcp'=> [
        'backlog' => 200,
        'so_reuseport' => true,
        'ipv6_v6only' => true
    ],
    // see http://php.net/manual/en/context.ssl.php for all ssl options
    'tls' => [
        'verify_peer' => false
    ]
];
$connector->connect('www.google.fr:80', $options)
    ->timeout(100)
    ->subscribe(
        function (\Rxnet\Socket\Connection $connection) use ($loop) {
            $connection
                ->subscribe(
                    function ($data) {
                        // Every chunk received will give onNext
                        var_dump($data);
                    },
                    function (\Exception $e) {
                        echo $e->getMessage();
                    },
                    function () {
                        echo 'completed';
                    }
                );
                $connection->write("GET /?gfe_rd=cr&dcr=0&ei=YWhsWsTDIZOm8wep_beACA HTTP/1.0\r\nHost: www.google.fr\r\n\r\n");
        }
    );

```

## Server
Low level socket server that will wait one input before saying ok and closing.

```php
<?php

$server = new \Rxnet\Socket\Server($loop);

$server->listen('0.0.0.0:9999')
    ->subscribe(function(\Rxnet\Socket\Connection $connection) {
        $connection->subscribe(function($data) use($connection) {
            echo 'received '.$data." from ".$connection->getRemoteAddress()." \n";
            $connection->end('ok');
        });
        $connection->write('Hello their');
    });
```

## Pipe
A Connection is an Observable and an Observer, you can subscribe a connection to another to pipe data.
