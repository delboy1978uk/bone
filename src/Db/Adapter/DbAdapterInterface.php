<?php

namespace Bone\Db\Adapter;

interface DbAdapterInterface
{
    /**
     * @param $credentials
     */
    public function __construct($credentials);

    public function openConnection();
    public function closeConnection();

    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @param $sql
     * @return mixed
     */
    public function executeQuery($sql);
}