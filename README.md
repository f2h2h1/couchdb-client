
Simple CouchDB Clinet
==============================

## 安装

安装 couchdb
```shell
docker run -p 5984:5984 -e COUCHDB_USER=admin -e COUCHDB_PASSWORD=password --name couchdb -d couchdb:3.1
```

克隆仓库
```shell
git clone https://github.com/f2h2h1/orm.git
```

运行 composer
```shell
composer install
```

运行测试用例
```shell
composer exec -v phpunit tests/CouchDBClientTest.php
```

## 使用例子
```php
use F2h2h1\CouchDB\SimpleClinet;

require_once(__DIR__ . '/../vendor/autoload.php');

$dbname = 'test_' . substr(md5(uniqid(microtime(true), true)), 0, mt_rand(3, 6));
$config = [
    'address' => 'http://127.0.0.1:5984',
    'user' => 'admin',
    'password' => 'password',
    'dbname' => $dbname,
];

$client = new SimpleClinet($config);

$client->deleteDatabase();
$client->createDatabase();
echo 'create database success' . PHP_EOL;

$data = ['name' => 'tony', 'age' => 23];
list($id, $rev) = $client->postDocument($data);
var_dump($id, $rev);

$docs = $client->findDocument($id);
var_dump($docs);

$data['age'] = 24;
list($id, $rev) = $client->putDocument($data, $id, $rev);
var_dump($id, $rev);

$docs = $client->findDocument($id);
var_dump($docs);

$client->deleteDocument($id, $rev);
echo 'delete document success' . PHP_EOL;

$client->deleteDatabase();
echo 'delete database success' . PHP_EOL;
```

[![License: MIT](https://img.shields.io/badge/license-MPL2.0-orange)](https://www.mozilla.org/en-US/MPL/2.0/)
