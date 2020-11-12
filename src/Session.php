<?php

namespace WindBridges\BrowserServerClient;


use Exception;
use GuzzleHttp\Client;

class Session
{
    protected $id;
    protected $name;
    protected $socketUri;
    protected $port;
    protected $client;

    public function __construct(Client $client, string $socketUri, string $name = null)
    {
        $this->client = $client;
        $this->socketUri = $socketUri;
        $this->name = $name;
        $tmp = explode('/', $this->socketUri);
        $this->id = array_pop($tmp);

        preg_match('!ws/(\d+)/!', $socketUri, $matches);

        if (!isset($matches[1])) {
            throw new Exception('Malformed Socket URI');
        }

        $this->port = $matches[1];
    }

    public function getPages(): array
    {
        $response = $this->client->get("/api/session/{$this->id}/status");
        $data = json_decode($response->getBody()->getContents(), true);
        $pages = [];

        foreach ($data['pages'] as $pageData) {
            $pages[] = new SessionPage($this, $pageData);
        }

        return $pages;
    }

    public function createPage(): SessionPage
    {
        $response = $this->client->get("/dt/{$this->port}/json/new");
        $data = json_decode($response->getBody()->getContents(), true);
        return new SessionPage($this, $data);
    }

    public function requirePage(string $name): SessionPage
    {
        $response = $this->client->get("/api/session/{$this->id}/page/require/{$name}");
        $data = json_decode($response->getBody()->getContents(), true);
        return new SessionPage($this, $data);
    }

    public function close(): void
    {
        $response = $this->client->get("/api/session/{$this->id}/close");
        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['success'])) {
            throw new Exception($data['error']);
        }
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSocketUri()
    {
        return $this->socketUri;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }


}