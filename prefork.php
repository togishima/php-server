<?php
echo 'ore-ore-prefork-php-server activated';
$sock = @socket_create_listen(8080);
if (!$sock) {
    exit('port in use and is not available');
}

$processes = 5;
$pids = [];
for ($i = 0; $i <= $processes; $i++) {
    $pid = pcntl_fork();
    if ($pid) {
        $pids[] = $pid;
    } else {
        response($sock);
    }
}
foreach ($pids as $pid) {
    pcntl_waitpid($pid, $status);
}
socket_close($sock);

function response($sock) {
    $client = socket_accept($sock);
    while (true) {
        $req = socket_read($client, 1024);
        if (preg_match('/GET ([^ ]+)/', $req, $m) && is_file('./public/' . $m[1])) {
            $header = "HTTP/1.1 200 OK\nContent-Type: text/html\nServer: OreOrePHPServer/0.1\n\n";
            $content = file_get_contents('./public/' . $m[1]);
            $res = $header . $content;
        } else {
            $res = "HTTP/1.1 404 Not Found\nContent-Type: text/html\nServer: OreOrePHPServer/0.1\n\nPage not found";
        }
        socket_write($client, $res);
        socket_close($client);
        $client = socket_accept($sock);
    }
}