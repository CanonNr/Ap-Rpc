<?php
namespace Canon\Rpc\Base;

abstract class Register
{
    /**
     * @var array
     * 所有注册的服务的数组 , 别名 => 完整类路径
     */
    public $services;

    abstract public function setService();

    public function __construct()
    {
        $this->services = $this->setService();
    }
}