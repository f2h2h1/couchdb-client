<?php
namespace F2h2h1\Tests\CouchDB;

use PHPUnit\Framework\TestCase;
use F2h2h1\CouchDB\SimpleClinet;

class CouchDBClientTest extends TestCase
{

    protected static $simpleClinet;
    protected static $dbname;

    protected static $dbh;

    public static function setUpBeforeClass(): void
    {
        $dbname = 'test_' . substr(md5(uniqid(microtime(true), true)), 0, mt_rand(3, 6));
        $config = [
            'address' => 'http://127.0.0.1:5984',
            'user' => 'admin',
            'password' => 'password',
            'dbname' => $dbname,
        ];

        $client = new SimpleClinet($config);
        self::$simpleClinet = $client;
        self::$dbname = $dbname;
        self::$simpleClinet->createDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        self::$simpleClinet->setDbname(self::$dbname)->deleteDatabase();
    }

    public function testPostDocument()
    {
        $data = ['name' => 'tony', 'age' => 23];
        $response = self::$simpleClinet->postDocument($data);
        $this->assertIsArray($response);
        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey(1, $response);

        return $response;
    }

    /**
     * @depends testPostDocument
     */
    public function testFindDocument($response)
    {
        $id = $response[0];
        $rev = $response[1];
        $docs = self::$simpleClinet->findDocument($id);
        $this->assertIsArray($docs);
        $this->assertArrayHasKey('name', $docs);
        $this->assertArrayHasKey('age', $docs);
        $this->assertEquals($docs['name'], 'tony');
        $this->assertEquals($docs['age'], 23);
        $this->assertEquals($docs['_id'], $id);
        $this->assertEquals($docs['_rev'], $rev);

        return $docs;
    }

    /**
     * @depends testFindDocument
     */
    public function testPutDocument($docs)
    {
        $id = $docs['_id'];
        $rev = $docs['_rev'];
        $docs['age'] = 24;
        list($id2, $rev2) = self::$simpleClinet->putDocument($docs, $id, $rev);
        $this->assertEquals($id, $id2);
        $this->assertNotEquals($rev, $rev2);
        $this->assertEquals($docs['age'], 24);
    }

    public function testOtehrDataBase()
    {
        $data = ['name' => 'tom', 'age' => 22];
        $newDbname = 'test_' . substr(md5(uniqid(microtime(true), true)), 0, mt_rand(3, 6));
        self::$simpleClinet->setDbname($newDbname)->createDatabase();

        $response = self::$simpleClinet->postDocument($data);
        $this->assertIsArray($response);
        $this->assertArrayHasKey(0, $response);
        $this->assertArrayHasKey(1, $response);
        $id = $response[0];

        $docs = self::$simpleClinet->findDocument($id);
        $this->assertIsArray($docs);
        $this->assertArrayHasKey('name', $docs);
        $this->assertArrayHasKey('age', $docs);
        $this->assertEquals($docs['name'], 'tom');
        $this->assertEquals($docs['age'], 22);

        self::$simpleClinet->setDbname($newDbname)->deleteDatabase();
    }
}
