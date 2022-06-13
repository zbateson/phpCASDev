<?php

define('CAS_VERSION_1_0', 'CAS_DEV');
define('CAS_VERSION_2_0', 'CAS_DEV');
define('CAS_VERSION_3_0', 'CAS_DEV');
define('SAML_VERSION_1_1', 'CAS_DEV');

/**
 * Mimicking the phpCAS class where necessary to override its behaviour for
 * development.
 *
 */
class phpCAS
{
    private static $host;
    private static $port;
    private static $context;

    /**
     * Overriding __callStatic so any function call not specifically defined
     * doesn't throw an exception.
     */
    public static function __callStatic($name, $arguments)
    {
        //do nothing
    }

    public static function client($version, $host, $port, $context)
    {
        static::$host = $host;
        static::$port = $port;
        static::$context = $context;
    }

    /**
     * Copied verbatim from phpCAS's Client.php
     * - changed preference to be $_SERVER['HTTP_HOST'] over
     *   $_SERVER['SERVER_NAME'] (which, with php -S returns 127.0.0.1
     *   instead of localhost when requested with localhost).
     *
     * @return string Server URL with domain:port
     */
    private static function _getClientUrl()
    {
        $server_url = '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            // explode the host list separated by comma and use the first host
            $hosts = explode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
            $server_url = $hosts[0];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
            $server_url = $_SERVER['HTTP_X_FORWARDED_SERVER'];
        } else {
            if (!empty($_SERVER['HTTP_HOST'])) {
                $server_url = $_SERVER['HTTP_HOST'];
            } else {
                $server_url = $_SERVER['SERVER_NAME'];
            }
        }
        if (!strpos($server_url, ':')) {
            if (empty($_SERVER['HTTP_X_FORWARDED_PORT'])) {
                $server_port = $_SERVER['SERVER_PORT'];
            } else {
                $ports = explode(',', $_SERVER['HTTP_X_FORWARDED_PORT']);
                $server_port = $ports[0];
            }

            if ( (static::_isHttps() && $server_port!=443)
                || (!static::_isHttps() && $server_port!=80)
            ) {
                $server_url .= ':';
                $server_url .= $server_port;
            }
        }
        return $server_url;
    }

    /**
     * Copied and modified slightly from phpCAS's Client.php
     *
     * @return The URL
     */
    public static function getURL()
    {
        // the URL is built when needed only
        $final_uri = '';
        // remove the ticket if present in the URL
        $final_uri = (static::_isHttps()) ? 'https' : 'http';
        $final_uri .= '://';

        $final_uri .= static::_getClientUrl();
        $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
        $final_uri .= $request_uri[0];

        if (isset($request_uri[1]) && $request_uri[1]) {
            $query_string= static::_removeParameterFromQueryString('ticket', $request_uri[1]);

            // If the query string still has anything left,
            // append it to the final URI
            if ($query_string !== '') {
                $final_uri  .= "?$query_string";
            }
        }
        return $final_uri;
    }

    /**
     * Copied verbatim from phpCAS's Client.php
     *
     * @param string $parameterName name of parameter
     * @param string $queryString   query string
     *
     * @return string new query string
     *
     * @link http://stackoverflow.com/questions/1842681/regular-expression-to-remove-one-parameter-from-query-string
     */
    private static function _removeParameterFromQueryString($parameterName, $queryString)
    {
        $parameterName  = preg_quote($parameterName);
        return preg_replace(
            "/&$parameterName(=[^&]*)?|^$parameterName(=[^&]*)?&?/",
            '', $queryString
        );
    }

    /**
     * Copied verbatim from phpCAS's Client.php
     *
     * @return bool true if https, false otherwise
     */
    private static function _isHttps()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        }
        if ( isset($_SERVER['HTTPS'])
            && !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] != 'off'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Redirects to a page with a username text field allowing a user to login
     * with any username for testing.
     */
    public static function forceAuthentication()
    {
        $str = 'http://';
        if (static::$port == 443) {
            $str = 'https://';
        }
        $str .= static::$host;
        if (!empty(static::$port) && static::$port != 80 && static::$port != 443) {
            $str .= ':' . static::$port;
        }
        $str .= static::$context . '?service=' . urlencode(static::getUrl());

        session_start();
        $str .= '&token=' . urlencode(session_id());
        header('Location: ' . $str);
        exit;
    }

    public static function isAuthenticated()
    {
        $manager = CASDevSessionManager::singleton();
        $session = $manager->getSessionFor(session_id());
        if (isset($session)) {
            return true;
        }
        return false;
    }

    public static function logoutWithRedirectService($path)
    {
        $manager = CASDevSessionManager::singleton();
        $manager->deleteSessionFor(session_id());
        header("Location: $path");
        exit;
    }

    public static function getUser()
    {
        $manager = CASDevSessionManager::singleton();
        $session = $manager->getSessionFor(session_id());
        if (isset($session)) {
            return $session->user;
        }
        return null;
    }

    public static function getAttributes()
    {
        $manager = CASDevSessionManager::singleton();
        $session = $manager->getSessionFor(session_id());
        if (isset($session)) {
            return $session->attributes;
        }
        return null;
    }

    public static function getAttribute($key)
    {
        $attribs = self::getAttributes();
        return (isset($attribs[$key])) ? $attribs[$key] : null;
    }
}
