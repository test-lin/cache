<?php

namespace Testlin\Cache\Driver;

interface Cache
{
    // 添加缓存
    public function set($key, $value, $time = '');

    // 获取缓存
    public function get($key);

    // 清空指定缓存
    public function del($key);

    // 清空所有缓存
    public function flush();

    // 递增
    public function increment($key, $number = 1);

    // 递减
    public function decrement($key, $number = 1);

    // 更新时间
    public function expire($key, $time);
}
