<?php

namespace think;
// 定义应用目录

define('APP_PATH', __DIR__ . '/application/');

// 定义应用缓存目录

define('RUNTIME_PATH', __DIR__ . '/runtime/');

// 开启调试模式

define('APP_DEBUG', true);

define('ROOT_PATH', __DIR__);

// 加载框架引导文件



require __DIR__ . '/thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
