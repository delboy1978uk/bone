<?php


use Bone\Db\Adapter\MySQL;


class BoneMvcDbAdapterMySQLTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var MySQL $db
     */
    protected $db;

    protected function _before()
    {
        $credentials = array(
            'host' => 'mariadb',
            'user' => 'travis',
            'pass' => 'drinkgrog',
            'database' => 'bone_db',
        );
        $this->db = new MySQL($credentials);
    }

    protected function _after()
    {
    }

    // make sure it can construct
    public function testOpenAndCloseConnection()
    {
        $this->assertNull($this->db->openConnection());
        $this->assertNull($this->db->closeConnection());
    }


    public function testGetConnection()
    {
        $this->assertInstanceOf('PDO',$this->db->getConnection());
        $this->db->closeConnection();
    }


    public function testConnectionStatusOk()
    {
        $this->db->openConnection();
        $this->assertTrue($this->db->isConnected());
        $this->db->closeConnection();
    }


    public function testNoConnection()
    {
        $this->assertFalse($this->db->isConnected());
    }




    public function testExecuteQuery()
    {
        // Not implemented yet
        $this->assertNull($this->db->executeQuery('select something from nothing'));
    }

}