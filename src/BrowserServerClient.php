<?php

namespace WindBridges\BrowserServerClient;

use Exception;
use GuzzleHttp\Client;

class BrowserServerClient
{
    protected $server;
    protected $host;
    protected $port;
    protected $client;

    public function __construct(string $server)
    {
        $this->server = $server;
        $tmp = explode(':', $server);
        $count = count($tmp);

        if ($count == 2) {
            $this->host = $tmp[0];
            $this->port = $tmp[1];
        } elseif ($count == 1) {
            $this->host = $tmp[0];
            $this->port = 80;
        } else {
            throw new Exception('Malformed server string, use "host:port" or "host"');
        }

        $this->client = new Client(['base_uri' => 'http://' . $this->server]);
    }

    public function auth($token)
    {

    }

    public function startSession(SessionOptions $options = null): Session
    {
        $options = $options ?: new SessionOptions();
        $response = $this->client->get('/api/session/start', ['json' => $options->toArray()]);
        $data = json_decode($response->getBody()->getContents(), true);

        return new Session($this->client, $data['socketUri'], $data['name']);
    }

    public function requireSession(string $name, SessionOptions $options = null): Session
    {
        $options = $options ?: new SessionOptions();
        $response = $this->client->get('/api/session/require/' . urlencode($name), ['json' => $options->toArray()]);
        $data = json_decode($response->getBody()->getContents(), true);

        return new Session($this->client, $data['socketUri'], $data['name']);
    }

    public function closeAllSessions(): int
    {
        $response = $this->client->get('/api/session/close/all');
        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['success'])) {
            throw new Exception($data['error']);
        } else {
            return count($data['sessionId']);
        }
    }

    public function getSessions(): array
    {
        $response = $this->client->get('/api/session/list');
        $data = json_decode($response->getBody()->getContents(), true);
        $sessions = [];

        foreach ($data['sessions'] as $sessionData) {
            $sessions[] = new Session($this->client, $sessionData['socketUri'], $sessionData['name']);
        }

        return $sessions;
    }
}