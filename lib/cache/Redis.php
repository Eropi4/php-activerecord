<?php
namespace ActiveRecord;

/**
 * Class Redis
 * @package ActiveRecord
 */
class Redis
{
	const DEFAULT_PORT = 6379;

	/** @var \Rediska */
	private $adapter;

	/**
	 * Creates a Redis instance.
	 *
	 * Takes an $options array w/ the following parameters:
	 *
	 * <ul>
	 * <li><b>host:</b> host for the Redis server </li>
	 * <li><b>port:</b> port for the Redis server </li>
	 * </ul>
	 *
	 * @param array $options
	 */
	public function __construct($options)
	{
		//$this->adapter = new \Redis();
		$options['port'] = isset($options['port']) ? $options['port'] : self::DEFAULT_PORT;

		$settings = [
			'servers' => [
				['host' => $options['host'], 'port' => $options['port']],
			]
		];
		$this->adapter = new \Rediska($settings);

		if (!$this->adapter)
		{
			throw new CacheException("Could not connect to $options[host]:$options[port]");
		}
	}

	/**
	 *
	 */
	public function flush()
	{
		$this->adapter->flushDb();
	}

	/**
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function read($key)
	{
		$data = $this->adapter->get($key);

		return $data ? $data : null;
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $expire
	 */
	public function write($key, $value, $expire)
	{
		$this->adapter->setAndExpire($key, $value, $expire);
	}

	/**
	 * @param $key
	 */
	public function delete($key)
	{
		$this->adapter->delete($key);
	}
}
