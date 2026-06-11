<?php
/**
 * Illuminate，Redis，Redis 管理器
 */

namespace Illuminate\Redis;

use InvalidArgumentException;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Redis\Connections\Connection;

/**
 * @mixin \Illuminate\Redis\Connections\Connection
 */
class RedisManager implements Factory
{
    /**
     * The application instance.
	 * 应用实例
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The name of the default driver.
	 * 默认驱动程序的名称
     *
     * @var string
     */
    protected $driver;

    /**
     * The Redis server configurations.
	 * Redis服务器配置
     *
     * @var array
     */
    protected $config;

    /**
     * The Redis connections.
	 * Redis的连接
     *
     * @var mixed
     */
    protected $connections;

    /**
     * Indicates whether event dispatcher is set on connections.
	 * 指示是否在连接上设置事件调度程序
     *
     * @var bool
     */
    protected $events = false;

    /**
     * Create a new Redis manager instance.
	 * 创建一个新的Redis管理器实例
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $driver
     * @param  array  $config
     * @return void
     */
    public function __construct($app, $driver, array $config)
    {
        $this->app = $app;
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Get a Redis connection by name.
	 * 通过名称获取Redis连接
     *
     * @param  string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->configure(
            $this->resolve($name), $name
        );
    }

    /**
     * Resolve the given connection by name.
	 * 按名称解析给定的连接
     *
     * @param  string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name = null)
    {
        $name = $name ?: 'default';

        $options = $this->config['options'] ?? [];

        if (isset($this->config[$name])) {
            return $this->connector()->connect($this->config[$name], $options);
        }

        if (isset($this->config['clusters'][$name])) {
            return $this->resolveCluster($name);
        }

        throw new InvalidArgumentException("Redis connection [{$name}] not configured.");
    }

    /**
     * Resolve the given cluster connection by name.
	 * 按名称解析给定的集群连接
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function resolveCluster($name)
    {
        $clusterOptions = $this->config['clusters']['options'] ?? [];

        return $this->connector()->connectToCluster(
            $this->config['clusters'][$name], $clusterOptions, $this->config['options'] ?? []
        );
    }

    /**
     * Configure the given connection to prepare it for commands.
	 * 配置给定的连接，以便为命令做好准备。
     *
     * @param  \Illuminate\Redis\Connections\Connection  $connection
     * @param  string  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    protected function configure(Connection $connection, $name)
    {
        $connection->setName($name);

        if ($this->events && $this->app->bound('events')) {
            $connection->setEventDispatcher($this->app->make('events'));
        }

        return $connection;
    }

    /**
     * Get the connector instance for the current driver.
	 * 获取当前驱动程序的连接器实例
     *
     * @return \Illuminate\Redis\Connectors\PhpRedisConnector|\Illuminate\Redis\Connectors\PredisConnector
     */
    protected function connector()
    {
        switch ($this->driver) {
            case 'predis':
                return new Connectors\PredisConnector;
            case 'phpredis':
                return new Connectors\PhpRedisConnector;
        }
    }

    /**
     * Return all of the created connections.
	 * 返回所有创建的连接
     *
     * @return array
     */
    public function connections()
    {
        return $this->connections;
    }

    /**
     * Enable the firing of Redis command events.
	 * 启用Redis命令事件的触发
     *
     * @return void
     */
    public function enableEvents()
    {
        $this->events = true;
    }

    /**
     * Disable the firing of Redis command events.
	 * 禁用Redis命令事件的触发
     *
     * @return void
     */
    public function disableEvents()
    {
        $this->events = false;
    }

    /**
     * Pass methods onto the default Redis connection.
	 * 将方法传递到默认的Redis连接
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }
}
