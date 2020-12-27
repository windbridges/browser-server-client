<?php

namespace WindBridges\BrowserServerClient;


class SessionOptions
{
    protected $windowSize = [1200, 900];
    protected $enableImages = true;
    protected $ignoreCertificateErrors = true;
    protected $proxy;
    /**
     * @var string|null Overrides user agent string for all pages of browser instance
     */
    protected $userAgent = null;

    public function toArray(): array
    {
        return [
            'windowSize' => $this->windowSize,
            'enableImages' => $this->enableImages,
            'ignoreCertificateErrors' => $this->ignoreCertificateErrors,
            'userAgent' => $this->userAgent,
            'proxy' => $this->proxy
        ];
    }

    /**
     * @return int[]
     */
    public function getWindowSize(): array
    {
        return $this->windowSize;
    }

    /**
     * @param int[] $windowSize
     */
    public function setWindowSize(array $windowSize): void
    {
        $this->windowSize = $windowSize;
    }

    /**
     * @return bool
     */
    public function imagesEnabled(): bool
    {
        return $this->enableImages;
    }

    /**
     * @param bool $enableImages
     */
    public function enableImages(bool $enableImages): void
    {
        $this->enableImages = $enableImages;
    }

    /**
     * @return bool
     */
    public function ignoreCertificateErrors(): bool
    {
        return $this->ignoreCertificateErrors;
    }

    /**
     * @param bool $ignoreCertificateErrors
     */
    public function setIgnoreCertificateErrors(bool $ignoreCertificateErrors): void
    {
        $this->ignoreCertificateErrors = $ignoreCertificateErrors;
    }

    /**
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getProxy(): string
    {
        return $this->proxy;
    }

    public function setProxy(string $proxy): void
    {
        $this->proxy = $proxy;
    }
}