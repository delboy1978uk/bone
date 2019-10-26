<?php

namespace Bone\Traits;

use Del\SessionManager;

trait HasSessionTrait 
{
    /** @var SessionManager $session */
    private $session;

    /**
     * @return SessionManager
     */
    public function getSession(): SessionManager
    {
        return $this->session;
    }

    /**
     * @param SessionManager $session
     */
    public function setSession(SessionManager $session): void
    {
        $this->session = $session;
    }

    
}