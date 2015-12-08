<?php
/**
 * Created by IntelliJ IDEA.
 * User: egor
 * Date: 08.12.15
 * Time: 10:54
 */

namespace ActiveRecord;


class ReconnectPDO
{
	/**
	 * @var string
	 */
	protected $dsn;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var array
	 */
	protected $driver_options;

	public function __construct($dsn, $username = "", $password = "", $driver_options = array())
	{
		$this->dsn = $dsn;
		$this->username = $username;
		$this->password = $password;
		$this->driver_options = $driver_options;
		$this->_connect();
	}
	public function __call($function, array $args = array())
	{
		try {
			$result = call_user_func_array(array($this->_connection(), $function), $args);
		} catch(\PDOException $e) {
			if ($e->getCode() != 'HY000' || !stristr($e->getMessage(), 'server has gone away')) {
				throw $e;
			}
			$this->reconnect();
			$result = call_user_func_array(array($this->_connection(), $function), $args);
		}
		return $result;
	}

	protected function _connection()
	{
		return $this->pdo instanceof \PDO ? $this->pdo : $this->_connect();
	}

	protected function _connect()
	{
		$this->pdo = new \PDO($this->dsn, $this->username, $this->password, (array) $this->driver_options);
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		return $this->pdo;
	}

	public function disconnect() {
		$this->pdo = null;
	}

	public function reconnect()
	{
		$this->disconnect();
		return (bool)$this->_connect();
	}
}