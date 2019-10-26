<?php

namespace Bone\Server;

use Del\SessionManager;

interface SessionAwareInterface
{
    /**
     * @param SessionManager $sessionManager
     */
    public function setSession(SessionManager $sessionManager): void;

    /**
     * @return SessionManager
     */
    public function getSession(): SessionManager;
}