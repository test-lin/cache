<?php

namespace Testlin\Cache;

class Cache
{
    protected $cache_driver;

    public function __construct($driver, $config)
    {
        if (is_null($this->cache_driver)) {
            $driver = strtolower($driver);
            if (in_array($driver, array('file', 'redis')) == false) {
                throw new \Exception("not driver");
            }

            if (!file_exists(__DIR__ . '/Driver/' . ucfirst($driver) . '.php')) {
                throw new \Exception("db driver [$driver] is not supported.");
            }
            $cache = __NAMESPACE__ . '\\Driver\\' . ucfirst($driver);

            $this->cache_driver = new $cache($config);
        }
    }

    public function init()
    {
        return $this->cache_driver;
    }
}