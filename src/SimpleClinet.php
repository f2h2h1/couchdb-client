<?php
namespace f2h2h1\CouchDB;

use Symfony\Component\HttpClient\HttpClient;

class SimpleClinet
{
    private $address = 'http://127.0.0.1:5984';
    private $user = 'admin';
    private $password = 'password';
    private $dbname;
    private $httpClient;

    function __construct(array $config)
    {
        if (isset($config['dbname'])) {
            // 抛异常
        }

        $this->address = $config['address'] ?? $this->address;
        $this->user = $config['user'] ?? $this->user;
        $this->password = $config['password'] ?? $this->password;
        $this->dbname = $config['dbname'];
        // $this->httpClient = HttpClient::create();

        $this->httpClient = HttpClient::createForBaseUri($this->address, [
            'auth_basic' => [$this->user, $this->password],
        ]);
    }

    public function createDatabase(string $dbname = ''): void
    {
        if ($dbname === '') {
            $dbname = $this->dbname;
        }

        $response = $this->httpClient->request('PUT', '/' . urlencode($dbname));
        $status = $response->getStatusCode();

        if ($status == 200 || $status == 412) {
            return;
        }

        // 抛异常
    }

    public function deleteDatabase(string $dbname = ''): void
    {
        if ($dbname === '') {
            $dbname = $this->dbname;
        }

        $response = $this->httpClient->request('DELETE', '/' . urlencode($dbname));
        $status = $response->getStatusCode();

        if ($status == 200 || $status == 404) {
            return;
        }

        // 抛异常
    }

    public function postDocument(array $data): array
    {
        $response = $this->httpClient->request('POST', '/' . $this->dbname, [
            'json' => $data,
        ]);

        if ($response->getStatusCode() != 201) {
            // 抛异常
        }

        $body = $response->toArray();

        if (!isset($body['id']) && !isset($body['rev'])) {
            // 抛异常
        }

        return [$body['id'], $body['rev']];
    }

    public function putDocument(array $data, string $id, ?string $rev = null): array
    {
        $data['_id'] = $id;
        if (!is_null($rev)) {
            $data['_rev'] = $rev;
        }

        $response = $this->httpClient->request('PUT', '/' . $this->dbname . '/' . urlencode($id), [
            'json' => $data,
        ]);

        if ($response->getStatusCode() != 201) {
            // 抛异常
        }

        $body = $response->toArray();

        if (!isset($body['id']) && !isset($body['rev'])) {
            // 抛异常
        }

        return [$body['id'], $body['rev']];
    }

    public function findDocument(string $id): array
    {
        $response = $this->httpClient->request('GET', '/' . $this->dbname . '/' . urlencode($id));
        return $response->toArray();
    }

    public function deleteDocument(string $id, string $rev): void
    {
        $response = $this->httpClient->request('DELETE', '/' . $this->dbname . '/' . urlencode($id) . '?rev=' . $rev);

        $status = $response->getStatusCode();

        if ($status == 200 || $status == 404) {
            return;
        }

        // 抛异常
    }
}
