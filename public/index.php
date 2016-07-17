<?php
namespace Framework;

spl_autoload_register(function ($class) {
	$tmp = explode("\\", $class);
	$file = '../framework/base/'.end($tmp).'.class.php';
	if (file_exists($file)) {
		require_once $file;
	}
}, false);

spl_autoload_register(function ($class) {
	$tmp = explode("\\", $class);
	require_once '../framework/exceptions/'.end($tmp).'.class.php';
}, false);

$route = $_SERVER['PATH_INFO'] === 'PATH_INFO' ? "/" : $_SERVER['PATH_INFO'];
$method = $_SERVER["REQUEST_METHOD"];
$headers = getallheaders();
$contentType = $headers["Content-Type"];
$body = [];

// 根据Content Type解析body
if (strcasecmp($method, 'POST') == 0 && isset($contentType)) {
    switch ($contentType) {
        case 'application/x-www-form-urlencoded':
            $body = $_POST;
            break;
        case 'application/json':
            $body = (array)json_decode(file_get_contents("php://input"));
            break;
        default:
            // 其他的先不管
            $body = $_POST;
            break;
    }
}

$request = new Request(
    $route,
    $method,
    $headers,
    $_GET,
    $body
);

Router::init();

require_once '../router.php';

try {
	$func = Router::routerMatch($request);
	$func($request);
}
catch (\Exception $exc) {
	echo $exc->getMessage();
}
