<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

use Moowgly\Lib\Utils\Logger;
use Moowgly\Lib\Utils\Chrono;
use Moowgly\Lib\Storage\ElasticDynamo;

// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../..'));

require APPLICATION_PATH . '/vendor/autoload.php';

$chronoPhp = new Chrono();
$chronoRouter = new Chrono();

// Create REST routeur
$router = new Zaphpa\Router();

// HTTP Method Overrides (CRUD support  via POST method)
$router->attach('Zaphpa\Middleware\MethodOverride');

// Ajax-friendly Endpoints
$router->attach('Zaphpa\Middleware\CORS');

// Authentification pre-route
$router->attach('Moowgly\Silo\Middleware\AuthMiddleware');

$router->addRoute(
    array(
        'path' => '/ws-moowgly/user/v1/',
        'get' => array('Moowgly\Silo\Controller\UserRoute', 'get'),
        'post' => array('Moowgly\Silo\Controller\UserRoute', 'post')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/user/v1/{id}',
        'get' => array('Moowgly\Silo\Controller\UserRoute', 'get'),
        'post' => array('Moowgly\Silo\Controller\UserRoute', 'post'),
    	'put' => array('Moowgly\Silo\Controller\UserRoute', 'put'),
    	'delete' => array('Moowgly\Silo\Controller\UserRoute', 'delete')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/checkUserToken/v1/',
        'get' => array('Moowgly\Silo\Controller\UserRoute', 'checkUserToken'),
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/checkUserPwdEmail/v1/',
        'get' => array('Moowgly\Silo\Controller\UserRoute', 'checkUserPwdEmail'),
    )
);
$router->addRoute(
    array(
        'path' => '/ws-moowgly/checkUserEmail/v1/',
        'get' => array('Moowgly\Silo\Controller\UserRoute', 'checkUserEmail'),
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/guest/v1/{id}',
        'get' => array('Moowgly\Silo\Controller\GuestRoute', 'get'),
        'post' => array('Moowgly\Silo\Controller\GuestRoute', 'insert'),
        'put' => array('Moowgly\Silo\Controller\GuestRoute', 'update'),
        'delete' => array('Moowgly\Silo\Controller\GuestRoute', 'delete')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/guest-delete/v1/{id}',
        'put' => array('Moowgly\Silo\Controller\GuestRoute', 'delete'),
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/host/v1/{id}',
        'get' => array('Moowgly\Silo\Controller\HostRoute', 'get'),
        'post' => array('Moowgly\Silo\Controller\HostRoute', 'insert'),
        'put' => array('Moowgly\Silo\Controller\HostRoute', 'update'),
        'delete' => array('Moowgly\Silo\Controller\HostRoute', 'delete')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/searchOffer/v1/',
        'get' => array('Moowgly\Silo\Controller\OfferRoute', 'search')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/searchHost/v1/',
        'get' => array('Moowgly\Silo\Controller\HostRoute', 'search')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/getActCat/v1/',
        'get' => array('Moowgly\Silo\Controller\ActivityRoute', 'getActCat')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/getActivity/v1/',
        'get' => array('Moowgly\Silo\Controller\ActivityRoute', 'get')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/language/v1/',
        'get' => array('Moowgly\Silo\Controller\LanguageRoute', 'get')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/currency/v1/',
        'get' => array('Moowgly\Silo\Controller\CurrencyRoute', 'get')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/country/v1/',
        'get' => array('Moowgly\Silo\Controller\CountryRoute', 'get')
    )
);

$router->addRoute(
    array(
        'path' => '/ws-moowgly/certificate/v1/',
        'get' => array('Moowgly\Silo\Controller\CertificateRoute', 'get')
    )
);

try {
    $router->route();
} catch (Zaphpa\Exceptions\InvalidPathException $e) {
    // Log exception message
    $msg = $e->getFile().':'.$e->getLine();
    $msg .= ' Error '.$e->getCode().' : '.$e->getMessage();
    $msg .= "\n".$e->getTraceAsString();
    Logger::getInstance()->error($msg);
    echo $msg;
    // Return 404 error
    header('HTTP/2.0 404 Not Found', true, 404);
}

Logger::getInstance()->debug('router configuration time='.$chronoRouter->stop().' sec');

Logger::getInstance()->debug('php process time='.$chronoPhp->stop().' sec');
