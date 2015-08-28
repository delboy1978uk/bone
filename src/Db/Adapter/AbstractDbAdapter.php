<?php

namespace Bone\Db\Adapter;
use Bone\Db\Adapter\DbAdapterInterface;
use PDO;

/**
 * Class AbstractDbAdapter
 * @package Bone\Db\Adapter
 */
abstract class AbstractDbAdapter implements DbAdapterInterface
{
    /**
     * @var PDO $connection
     */
    protected  $connection;
    /**
     * @var string
     */
    private   $host;
    /**
     * @var string
     */
    private   $database;
    /**
     * @var string
     */
    private   $user;

    /** @var string $pass */
    private $pass;

    /**
     * @param $credentials
     */
    public function __construct($credentials)
    {
        $this->host = $credentials['host'];
        $this->database = $credentials['database'];
        $this->user = $credentials['user'];
        $this->pass = $credentials['pass'];
    }

    /**
     * @return mixed
     */
    public abstract function openConnection();

    /**
     * @return mixed
     */
    public abstract function closeConnection();

    /**
     * @return bool
     */
    public function isConnected()
    {
        if(!$this->connection)
        {
            return false;
        }
        return true;
    }

    /**
     * @param $sql
     * @return mixed|null
     */
    public function executeQuery($sql)
    {
        // @todo: Implement executeQuery() method.
        return null;
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        if(!$this->connection)
        {
            $this->openConnection();
        }
        return $this->connection;
    }

    /**
     * @return string|null
     */
    protected  function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return string|null
     */
    protected function getHost()
    {
        return $this->host;
    }

    /**
     * @return string|null
     */
    protected function getPass()
    {
        return $this->pass;
    }

    /**
     * @return string|null
     */
    protected function getUser()
    {
        return $this->user;
    }


}

