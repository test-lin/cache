<?php

require __DIR__ . '/../init.php';

use Testlin\Cache\Cache;

$config = [
    'cache' => [
        'type' => 'file',
        'file' => [
            'cache_path' => '/var/www/study/cache/tmp',
            'cache_prefix' => 'test'
        ],
        'redis' => [
        ]
    ]
];

$cache_type = $config['cache']['type'];
$cache_config = $config['cache'][$cache_type];
$cache = new Cache($cache_type, $cache_config);
$cache = $cache->init();

// $cache->del('abc');

// $cache->set('abc', ['state' => 1, 'msg' => '用户 xxx 添加 [abc] 用户', 'created' => time()], 1);

// $cache->expire('abc', 10);

// print_r($cache->get('abc'));exit;

// $cache->set('number', 5);

$num = $cache->increment('number');
var_dump($num);

// $num = $cache->decrement('number');
// var_dump($cache->get('number'));exit;