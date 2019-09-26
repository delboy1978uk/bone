<?php

namespace Bone\Server;

class SiteConfig
{
    /** @var string $title */
    private $title;
    
    /** @var string $domain  */
    private $domain;
    
    /** @var string $contactEmail  */
    private $contactEmail;
    
    /** @var string $serverEmail */
    private $serverEmail;

    /** @var string $company */
    private $company;

    /** @var string $address */
    private $address;

    /** @var string $logo */
    private $logo;
    
    /** @var Environment $environment */
    private $environment;

    /**
     * SiteConfig constructor.
     * @param array $config
     * @param Environment $environment
     */
    public function __construct(array $config, Environment $environment)
    {
        $this->title = $config['title'];
        $this->domain = $config['domain'];
        $this->contactEmail = $config['contactEmail'];
        $this->serverEmail = $config['serverEmail'];
        $this->company = $config['company'];
        $this->address = $config['address'];
        $this->logo = $config['logo'];
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail(string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return string
     */
    public function getServerEmail(): string
    {
        return $this->serverEmail;
    }

    /**
     * @param string $serverEmail
     */
    public function setServerEmail(string $serverEmail): void
    {
        $this->serverEmail = $serverEmail;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }
}
