<?php
spl_autoload_register(function ($class) {
    $class = explode('\\', $class);
    $namespace = $class[0];
    $className = $class[1];
    require_once __DIR__ . "/Classes/{$namespace}/{$className}.php";
});