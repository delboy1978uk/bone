<?php

namespace Bone\Server;

use Bone\Traits\HasAttributesTrait;

class SiteConfig
{
    use HasAttributesTrait;

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
        $this->title = $config['site']['title'];
        $this->domain = $config['site']['domain'];
        $this->baseUrl = $config['site']['baseUrl'];
        $this->contactEmail = $config['site']['contactEmail'];
        $this->serverEmail = $config['site']['serverEmail'];
        $this->company = $config['site']['company'];
        $this->address = $config['site']['address'];
        $this->logo = $config['site']['logo'];
        $this->emailLogo = $config['site']['emailLogo'];
        $this->environment = $environment;
        $this->setAttributes($config);
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
