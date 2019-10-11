<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

/**
 * base url
 */
$baseURL = 'http://localhost/index.php/';

/**
 * default controller
 */
$defaultController = 'race_controller';

/**
 * default controller method
 */
$defaultMethod = 'index';

/**
 * application directory
 */
$applicationFolder = DIRECTORY_SEPARATOR . 'application';

/**
 * model directory
 */
$modelFolder = '';

/**
 * view directory
 */
$viewFolder = '';

/**
 * controller directory
 */
$controllerFolder = '';

/**
 * library directory
 */
$libraryFolder = '';

define('BASEURL', $baseURL);

define('APPPATH', __DIR__ . $applicationFolder . DIRECTORY_SEPARATOR);

$modelFolder = APPPATH . 'models';
define('MODELPATH', $modelFolder . DIRECTORY_SEPARATOR);

$viewFolder = APPPATH . 'views';
define('VIEWPATH', $viewFolder . DIRECTORY_SEPARATOR);

$controllerFolder = APPPATH . 'controllers';
define('CONTROLLERPATH', $controllerFolder . DIRECTORY_SEPARATOR);

$libraryFolder = APPPATH . 'libraries';
define('LIBRARYPATH', $libraryFolder . DIRECTORY_SEPARATOR);

/**
 * if strinf ends with another string
 * @param $haystack
 * @param $needle
 * @return bool
 */
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * auto load classes
 */
spl_autoload_register(function ($className) {

    if (endsWith($className, '_controller')) {
        $filename = preg_replace('/_controller$/', '', $className);
        $filename .= '.php';
        if (file_exists(CONTROLLERPATH . $filename)) {
            include CONTROLLERPATH . $filename;
        }
    }

    if (endsWith($className, '_model')) {
        $filename = preg_replace('/_model$/', '', $className);
        $filename .= '.php';
        if (file_exists(MODELPATH . $filename)) {
            include MODELPATH . $filename;
        }
    }

    if (endsWith($className, '_library')) {
        $filename = preg_replace('/_library$/', '', $className);
        $filename .= '.php';
        if (file_exists(LIBRARYPATH . $filename)) {
            include LIBRARYPATH . $filename;
        }
    }

});

$patterns = [
    '/^(\/index\.php[\/]?|\/)/',
    '/\/$/'
];
$requestURI = explode('/', preg_replace($patterns, '', $_SERVER['REQUEST_URI']));

if (count($requestURI) == 1 && empty($requestURI[0])) {
    $controller = $defaultController;
} else if (isset($requestURI[0])) {
    $controller = $requestURI[0] . '_controller';
} else {
    // class doesnt exist
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
    die;
}

$controllerObject = new $controller();

if (isset($requestURI[1])) {
    $method = $requestURI[1];
} else {
    $method = $defaultMethod;
}

if (!is_callable([$controllerObject, $method])) {
    // method is not callable
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Found';
    die;
}

if (isset($requestURI[2])) {
    // call method and pass parameters
    $params = array_slice($requestURI, 2);
    call_user_func_array(array($controllerObject, $method), $params);
} else {
    // call method
    $controllerObject->$method();
}