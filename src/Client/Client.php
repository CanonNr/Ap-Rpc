<?php

namespace Canon\Rpc\Client;

use Canon\Rpc\Base\Register;
class Client
{
    public $host;

    public $port;

    public $config;

    public $client;

    public $registerClass = \App\Rpc\rpc::class;

    public $class;

    public $method;

    public $args;
    
    public function __construct($host,$port,array $config = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->config = $config;
        $this->setSwooleClient();
        $this->isExistSwoole();
    }

    public function isExistSwoole(){
        if (!extension_loaded('swoole')) {
            echo 'Swoole extension not installed';
            die();
        }
    }

    public function setSwooleClient()
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9501, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $this->client = $client;
    }

    public function class($className)
    {
        $rpc = new $this->registerClass();
        $this->class = $rpc->services[$className];
        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->method = $name;
        $this->args = $arguments;
        $this->send();
    }

    public function send()
    {
        $data = [
            'class'  => $this->class,
            'method' => $this->method,
            'args'   => $this->args
        ];
        $client = $this->client;
        $client->send(json_encode($data));
    }
}