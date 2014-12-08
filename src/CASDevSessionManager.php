<?php

/**
 * Stores an authentication token associated with a remote user that logged in,
 * and associated login details.
 * 
 * Necessary because the server running the project may be different from the
 * one running phpCASDev.
 */
class CASDevSessionManager
{
    protected $config;
    protected $sessions;
    
    private function __construct()
    {
        $this->config = CASDevConfig::singleton();
        $this->readSessions();
    }
    
    public static function singleton()
    {
        static $singleton;
        if (!isset($singleton)) {
            $singleton = new CASDevSessionManager();
        }
        return $singleton;
    }
    
    public function loginAs(CASDevSession $s)
    {
        $this->sessions[$s->token] = $s;
        $this->writeSessions();
    }
    
    public function getSessionFor($token)
    {
        if (isset($this->sessions[$token])) {
            $session = $this->sessions[$token];
            if (time() - $session->time < $this->config->sessionExpiryTime) {
                return $this->sessions[$token];
            }
        }
        return null;
    }
    
    public function deleteSessionFor($token)
    {
        if (isset($this->sessions[$token])) {
            unset($this->sessions[$token]);
            $this->writeSessions();
        }
    }
    
    protected function readSessions()
    {
        if (is_readable($this->config->sessionFile)) {
            $this->sessions = unserialize(file_get_contents($this->config->sessionFile));
        }
    }
    
    protected function writeSessions()
    {
        $sessions = [];
        foreach ($this->sessions as $session) {
            if (time() - $session->time < $this->config->sessionExpiryTime) {
                $sessions[$session->token] = $session;
            }
        }
        @file_put_contents($this->config->sessionFile, serialize($sessions));
        @chmod($this->config->sessionFile, 0766);
    }
}
