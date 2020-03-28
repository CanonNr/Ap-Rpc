<?php
namespace Canon\Rpc\Server;

use swoole_server;

abstract class Server
{
    public $host;

    public $port;

    public $config;

    public $services = [];

    abstract public function handle();

    public function __construct($host,$port,$config = [])
    {
        $this->host = $host;
        $this->port = intval($port);
        $this->config = $config;
        $this->isExistSwoole();
    }

    public function isExistSwoole(){
        if (!extension_loaded('swoole')) {
            echo 'Swoole extension not installed';
            die();
        }
    }

    public function receive(swoole_server $server, $fd, $reactor_id, $data)
    {
        $data = json_decode($data,true);
        $result = call_user_func_array([$data['class'],$data['method']],$data['args']);
        $server->send($fd,$result."\n");
    }
}