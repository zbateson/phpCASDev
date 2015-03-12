# phpCASDev (in development, pre-beta)

Simple development/testing environment for CAS authentication in PHP.  Allows bypassing requests to a CAS server, serving a local login page with a single password and an open user field.

The package is intended as a drop-in replacement for jasig/phpcas.  The arguments sent to phpCAS::client should be all that needs to be changed.

## Installation

```
composer require-dev phpcas-dev
```

> phpcas-dev 'provides' jasig/phpcas, and overrides phpCAS.


## Configuration

phpCASDev uses configuration options in composer.json.  Default settings are taken from phpCASDev's composer.json, but overridden if installed as a library for another project.

## Usage

```
php -S 127.0.0.1 -t vendor/zbateson/phpcasdev/web
