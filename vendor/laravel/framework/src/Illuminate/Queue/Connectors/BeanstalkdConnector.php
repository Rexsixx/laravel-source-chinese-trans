<?php
/**
 * Illuminate，队列，连接器，Beanstalkd 连接器
 */

namespace Illuminate\Queue\Connectors;

use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;
use Illuminate\Queue\BeanstalkdQueue;

class BeanstalkdConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
	 * 建立队列连接
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new BeanstalkdQueue(
            $this->pheanstalk($config),
            $config['queue'],
            $config['retry_after'] ?? Pheanstalk::DEFAULT_TTR,
            $config['block_for'] ?? 0
        );
    }

    /**
     * Create a Pheanstalk instance.
	 * 创建一个Pheanstalk实例
     *
     * @param  array  $config
     * @return \Pheanstalk\Pheanstalk
     */
    protected function pheanstalk(array $config)
    {
        return Pheanstalk::create(
            $config['host'],
            $config['port'] ?? Pheanstalk::DEFAULT_PORT,
            $config['timeout'] ?? Connection::DEFAULT_CONNECT_TIMEOUT
        );
    }
}
