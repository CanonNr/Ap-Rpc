<?php

namespace Canon\Rpc\Client;

use Canon\Rpc\Base\Register;
class Client
{
    private static $instances = [];

    private $host;

    private $port;

    private $config;

    private $client;

    private $registerClass = \App\Rpc\rpc::class;

    private $class;

    private $method;

    private $args;
    
    public function __construct()
    {
        $this->isExistSwoole();
    }

    public function set($host,$port,array $config = [])
    {
        $this->host = $host;
        $this->port = $port;
        $this->config = $config;
        $this->setSwooleClient();
        return $this;
    }

    public static function getInstance()
    {
        $key = get_called_class();
        
        if (isset(static::$instances[$key]) && static::$instances[$key] instanceof static) {
            return static::$_instances[$key];
        }

        return static::$instances[$key] = new static();
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
        if (!$client->connect($this->host, $this->port, -1)) {
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
        return rtrim($this->send(),"\n");
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
        $result = $client->recv();
        $client->close();
        return $result;

    }
}