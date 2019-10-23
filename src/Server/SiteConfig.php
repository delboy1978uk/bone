<?php

namespace Bone\Server;

class SiteConfig
{
    /** @var string $title */
    private $title;

    /** @var string $domain  */
    private $domain;

    /** @var string $baseUrl  */
    private $baseUrl;
    
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

    /** @var string $emailLogo */
    private $emailLogo;
    
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
        $this->baseUrl = $config['baseUrl'];
        $this->contactEmail = $config['contactEmail'];
        $this->serverEmail = $config['serverEmail'];
        $this->company = $config['company'];
        $this->address = $config['address'];
        $this->logo = $config['logo'];
        $this->emailLogo = $config['emailLogo'];
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
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @return string
     */
    public function getServerEmail(): string
    {
        return $this->serverEmail;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getEmailLogo(): string
    {
        return $this->emailLogo;
    }


}
