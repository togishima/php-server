<?php
echo 'ore-ore-php-server activated';
$sock = @socket_create_listen(8081);
if (!$sock) {
    exit('port in use and is not available');
}

$client = socket_accept($sock);
while (true) {
    $req = socket_read($client, 1024);
    if (preg_match('/GET ([^ ]+)/', $req, $m)) {
        $res = "HTTP/1.1 200 OK\nContent-Type: text/html\nServer: OreOrePHPServer/0.1\n\n";
        $content = 'hello oreore-php server';
        socket_write($client, $res . $content);
    }
    socket_close($client);
    $client = socket_accept($sock);
}
socket_close($sock);
