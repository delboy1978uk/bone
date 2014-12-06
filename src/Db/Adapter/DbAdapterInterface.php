<?php

namespace Bone\Db\Adapter;

interface DbAdapterInterface
{
    /**
     * @return void
     */
    public function __construct($credentials);
    public function openConnection();
    public function closeConnection();

    /**
     * @return void
     */
    public function isConnected();

    /**
     * @return void
     */
    public function executeQuery();
}