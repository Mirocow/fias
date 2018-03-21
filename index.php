<?php
require_once __DIR__ . '/vendor/autoload.php';

$container = new Application();
$logger    = $container->getLogger();
$dispatcher = $container->getDispatcher();

$uri = $_SERVER['REQUEST_URI'];

// Remove ? from our route path
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, $pos + 1, strlen($uri));
}

// Hack
if (strlen($uri) > 1) {
    $uri = rtrim(rawurldecode($uri), '/');
}

$container->routeExecute($dispatcher, $uri);