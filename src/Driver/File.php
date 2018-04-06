<?php

namespace Testlin\Cache\Driver;

class File implements Cache
{
    protected $config;

    public function __construct($config)
    {
        $config = array(
            'cache_path' => isset($config['cache_path']) ? $config['cache_path'] : sys_get_temp_dir(),
            'cache_prefix' => isset($config['cache_prefix']) ? $config['cache_prefix'] : ''
        );

        $config['cache_path'] = str_replace('//', '/', $config['cache_path'].'/');
        $this->config = $config;
    }

    // 添加缓存
    public function set($key, $value, $time = '')
    {
        $path = $this->config['cache_path'] . $this->config['cache_prefix'] . $key;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $value = array(
            'data' => $value
        );
        $data = serialize($value);

        $state = file_put_contents($path, $data, LOCK_EX);
        if ($state === false) {
            return false;
        }

        if (is_numeric($time) && 0 < $time) {
            return $this->expire($key, $time);
        }

        return true;
    }

    // 获取缓存
    public function get($key)
    {
        $path = $this->config['cache_path'] . $this->config['cache_prefix'] . $key;

        if (!file_exists($path)) {
            return false;
        }
        $value = file_get_contents($path);
        $value = unserialize($value);

        $time = isset($value['time']) ? $value['time'] : '';
        if (is_numeric($time) && $time < time()) {
            $this->del($key);
            return false;
        }

        return $value['data'];
    }

    // 清空指定缓存
    public function del($key)
    {
        $path = $this->config['cache_path'] . $this->config['cache_prefix'] . $key;

        return unlink($path);
    }

    // 清空所有缓存
    public function flush()
    {
        $dir = $this->config['cache_path'];

        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                }
            }
        }

        closedir($dh);
        return true;
    }

    // 递增
    public function increment($key, $number = 1)
    {
        $value = $this->get($key);
        if (!is_numeric($value)) {
            return false;
        }

        $value += $number;

        $this->set($key, $value);
        return $this->get($key);
    }

    // 递减
    public function decrement($key, $number = 1)
    {
        $value = $this->get($key);
        if (!$this->get($key)) {
            return false;
        }

        $value -= $number;

        $this->set($key, $value);

        return $this->get($key);
    }

    // 更新时间
    // 时间计量 分
    public function expire($key, $time)
    {
        $value = $this->get($key);
        $path = $this->config['cache_path'] . $this->config['cache_prefix'] . $key;

        if (is_numeric($time) && 0 < $time) {
            $time = time() + ($time * 60);
        }

        $value = array(
            'data' => $value,
            'time' => $time
        );
        $data = serialize($value);

        return file_put_contents($path, $data, LOCK_EX);
    }

}