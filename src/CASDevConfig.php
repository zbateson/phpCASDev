<?php

/**
 * Loads default password and attributes from composer.json.  Attempts to check
 * if the library was installed as part of another library and uses that parent
 * library's composer.json instead.
 */
class CASDevConfig
{
    private $settings = [
        'password' => 'changeme',
        'defaultAttributes' => 'USER',
        'sessionFile' => 'auth-map',
        'sessionExpiryTime' => 20,
    ];
    
    private function __construct()
    {
        $this->loadSettings();
    }
    
    public static function singleton()
    {
        static $singleton;
        if (!isset($singleton)) {
            $singleton = new CASDevConfig();
        }
        return $singleton;
    }
    
    public function __get($name)
    {
        if (isset($this->settings[$name])) {
            return $this->settings[$name];
        }
        return null;
    }
    
    public function __isset($name)
    {
        return isset($this->settings[$name]);
    }
    
    protected function loadSettings()
    {
        $pkgConfig = $this->loadFromConf(rtrim(dirname(__DIR__), '/\\') . '/composer.json');
        
        $location = basename(dirname(__DIR__));
        $parent = basename(dirname(dirname(__DIR__)));
        if ($pkgConfig->name === "$parent/$location") {
            $parentConfig = rtrim(dirname(dirname(dirname(dirname(__DIR__)))), '/\\') . '/composer.json';
            if (is_readable($parentConfig)) {
                $this->loadFromConf($parentConfig);
            }
        }
    }
    
    protected function loadFile($path)
    {
        return json_decode(file_get_contents($path));
    }
    
    protected function loadFromConf($path)
    {
        $conf = $this->loadFile($path);
        $partSettings = $conf->extra->casdev;
        foreach ($this->settings as $key => $value) {
            if (isset($partSettings->$key)) {
                $this->settings[$key] = $partSettings->$key;
                if ($key === 'sessionFile') {
                    $filePath = $this->settings[$key];
                    if ($filePath[0] !== '/' && $filePath[0] !== '\\') {
                        $filePath = rtrim(dirname($path), '\\/') . '/' . $filePath;
                    }
                    $this->settings[$key] = $filePath;
                }
            }
        }
        return $conf;
    }
}
