<?php
namespace Canon\Rpc;

class Server
{
    public $host;

    public $port;

    public $config;

    public $services = [];

    public function __construct($host,$port,$config)
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

    public function start()
    {

    }
}