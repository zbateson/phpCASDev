<?php

/**
 * Stores an authentication token associated with a remote user that logged in,
 * and associated login details.
 * 
 * Necessary because the server running the project may be different from the
 * one running phpCASDev.
 */
class CASDevSession
{
    public $token;
    public $user;
    public $attributes;
    public $time;
}
