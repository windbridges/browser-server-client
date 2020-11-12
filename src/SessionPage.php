<?php

namespace WindBridges\BrowserServerClient;


use Exception;

class SessionPage
{
    protected $targetId;
    protected $description;
    protected $title;
    protected $url;
    protected $socketUri;
    protected $devtoolsFrontendUrl;

    private $session;

    public function __construct(Session $session, array $data)
    {
        $this->session = $session;
        $this->targetId = $data['id'];
        $this->description = $data['description'];
        $this->title = $data['title'];
        $this->url = $data['url'];
        $this->socketUri = $data['webSocketDebuggerUrl'];
        $this->devtoolsFrontendUrl = $data['devtoolsFrontendUrl'];
    }

    public function activate(): void
    {
        $response = $this->session->getClient()->get("/dt/{$this->session->getPort()}/json/activate/{$this->targetId}");

        if ($response->getBody()->getContents() != 'Target activated') {
            throw new Exception('Unable to activate target');
        }
    }

    public function close(): void
    {
        $response = $this->session->getClient()->get("/dt/{$this->session->getPort()}/json/close/{$this->targetId}");

        if ($response->getBody()->getContents() != 'Target is closing') {
            throw new Exception('Unable to close target');
        }
    }

    /**
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getSocketUri()
    {
        return $this->socketUri;
    }

    /**
     * @return mixed
     */
    public function getDevtoolsFrontendUrl()
    {
        return $this->devtoolsFrontendUrl;
    }


}