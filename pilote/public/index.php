<?php
//var_dump($_SERVER);die;
use Moowgly\Lib\Utils\Logger;

// Define application path
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../..'));
// Define application version
define('VERSION_NUMBER', '1.0.0');

require APPLICATION_PATH . '/vendor/autoload.php';

// Create REST routeur
$router = new Zaphpa\Router();

// HTTP Method Overrides (CRUD support via POST method)
$router->attach('Zaphpa\Middleware\MethodOverride');

// Ajax-friendly Endpoints
$router->attach('Zaphpa\Middleware\CORS');

// Authentification pre-route
$router->attach('Moowgly\Pilote\Middleware\AuthMiddleware');

$pathmoowgly = 'moowgly';

// Routes
$router->addRoute(array(
    'path' => $pathmoowgly . '/page/{page}',
    'get' => array('Moowgly\Pilote\Controller\FrontController','show'),
));

$router->addRoute(array(
    'path' => $pathmoowgly,
    'get' => array('Moowgly\Pilote\Controller\IndexController','index'),
    'post' => array('Moowgly\Pilote\Controller\IndexController','insert')
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/mission',
    'get' => array('Moowgly\Pilote\Controller\IndexController','mission'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/become-host_{tab}',
    'get' => array('Moowgly\Pilote\Controller\RegisterController','index'),
    'post' => array('Moowgly\Pilote\Controller\RegisterController','insertHost')
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_dashboard',
    'get' => array('Moowgly\Pilote\Controller\GuestController','dashboard'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_messages',
    'get' => array('Moowgly\Pilote\Controller\GuestController','messages'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_messages_conversation',
    'get' => array('Moowgly\Pilote\Controller\GuestController','conversation'),
    'post' => array('Moowgly\Pilote\Controller\MessageController','insert'),
    'put' => array('Moowgly\Pilote\Controller\MessageController','update'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_bookings_bookings',
    'get' => array('Moowgly\Pilote\Controller\GuestController','bookings'),
    'post' => array('Moowgly\Pilote\Controller\BookingController','insert'),
    'put' => array('Moowgly\Pilote\Controller\BookingController','update'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_bookings_send_email',
    'post' => array('Moowgly\Pilote\Controller\GuestController','sendEmail')
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_reviews',
    'get' => array('Moowgly\Pilote\Controller\GuestController','reviews'),
    'post' => array('Moowgly\Pilote\Controller\ReviewController','insert'),
    'put' => array('Moowgly\Pilote\Controller\ReviewController','upate'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_profile',
    'get' => array('Moowgly\Pilote\Controller\GuestController','profile'),
    'post' => array('Moowgly\Pilote\Controller\GuestController','insert'),
    'put' => array('Moowgly\Pilote\Controller\GuestController','update'),
));

/*
* Temporary routing waiting Ajax Post
*/

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_profile_insert',
    'get' => array('Moowgly\Pilote\Controller\GuestController','insert'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_profile_update',
    'get' => array('Moowgly\Pilote\Controller\GuestController','update'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_profile_update_kid',
    'get' => array('Moowgly\Pilote\Controller\GuestController','addKid'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_profile_delete',
    'get' => array('Moowgly\Pilote\Controller\GuestController','delete'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/host_profile_profile_insert',
    'get' => array('Moowgly\Pilote\Controller\HostController','insert'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/host_profile_profile_get',
    'get' => array('Moowgly\Pilote\Controller\HostController','profile'),
));


$router->addRoute(array(
    'path' => $pathmoowgly . '/host_profile_profile_update',
    'get' => array('Moowgly\Pilote\Controller\HostController','update'),
));

/*
* User / Login / routing
*/

$router->addRoute(array(
    'path' => $pathmoowgly . '/registerUser',
    'get' => array('Moowgly\Pilote\Controller\LoginController','checkLoginAndRegister'),
    'post' => array('Moowgly\Pilote\Controller\LoginController','checkLoginAndRegister')
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/recoverPassword',
    'post' => array('Moowgly\Pilote\Controller\LoginController','recoverPassword')
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/validationRegistration/{id}/{token}',
    'get' => array('Moowgly\Pilote\Controller\LoginController','validInscription'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/login_test',
    'post' => array('Moowgly\Pilote\Controller\LoginController','login'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/testSendMail',
    'get' => array('Moowgly\Pilote\Controller\LoginController','testSendMail'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/searchOfferTest',
    'get' => array('Moowgly\Pilote\Controller\SearchController','searchOfferTest'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/searchHostTest',
    'get' => array('Moowgly\Pilote\Controller\SearchController','searchHostTest'),
));

/*
* Temporary routing end
*/

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest_profile_kids',
    'get' => array('Moowgly\Pilote\Controller\GuestController','kids'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/guest-card_guest-card',
    'get' => array('Moowgly\Pilote\Controller\GuestController','guestCard'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_dashboard',
    'get' => array('Moowgly\Pilote\Controller\HostController','dashboard'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_profile_profile',
    'get' => array('Moowgly\Pilote\Controller\HostController','profile'),
    'put' => array('Moowgly\Pilote\Controller\HostController','update'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_messages_messages',
    'get' => array('Moowgly\Pilote\Controller\HostController','messages'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_messages_conversation',
    'get' => array('Moowgly\Pilote\Controller\HostController','conversation'),
    'put' => array('Moowgly\Pilote\Controller\MessageController','update'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_bookings_bookings',
    'get' => array('Moowgly\Pilote\Controller\HostController','bookings'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/host_bookings_send_email',
    'post' => array('Moowgly\Pilote\Controller\HostController','sendEmail'),
));


$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_offers_create-offer',
    'get' => array('Moowgly\Pilote\Controller\OfferController','createOffer'),
    'post' => array('Moowgly\Pilote\Controller\OfferController','insert'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_offers_offers',
    'get' => array('Moowgly\Pilote\Controller\OfferController','offers')
));


$router->addRoute(array(
    'path' => $pathmoowgly . '/dashboard_reviews_reviews',
    'get' => array('Moowgly\Pilote\Controller\HostController','reviews'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/host-card_host-card',
    'get' => array('Moowgly\Pilote\Controller\HostController','hostCard'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/search-results_search-results',
    'get' => array('Moowgly\Pilote\Controller\SearchController','search'),
));

$router->addRoute(array(
    'path' => $pathmoowgly . '/{id}',
    'get' => array('Moowgly\Pilote\Controller\IndexController','index'),
    'put' => array('Moowgly\Pilote\Controller\IndexController','update'),
    'delete' => array('Moowgly\Pilote\Controller\IndexController','delete')
));

try {
    $router->route();
} catch (Zaphpa\Exceptions\InvalidPathException $e) {
    // Log exception message
    $msg = $e->getFile() . ':' . $e->getLine();
    $msg .= ' Error ' . $e->getCode() . ' : ' . $e->getMessage();
    $msg .= "\n" . $e->getTraceAsString();
    Logger::getInstance()->error($msg);

    // Return 404 error
    header('HTTP/2.0 404 Not Found', true, 404);
}

function getTwig()
{
    // Twig template engine
    \Twig_Autoloader::register();

    $loader = new \Twig_Loader_Filesystem(APPLICATION_PATH . '/pilote/src/static/twig');
    $twig = new \Twig_Environment($loader, array(
      'cache' => false, //APPLICATION_PATH . '/pilote/src/static/twig/compile',
    ));

    /**
     * Check current url
     */
    $twig->addFunction(new Twig_SimpleFunction('menuActive', function ($check, $return = 'active') {
        $uri = trim(strtolower($_SERVER['REQUEST_URI']));
        $check = trim(strtolower($check));
        return strpos($uri, $check) !== false ? $return : false;
    }));

    return $twig;
}

function getTime($msg)
{
    // return;
    static $start;
    if(!isset($start)) {
        $start = (float) array_sum(explode(' ', microtime()));
    }
    $end = (float) array_sum(explode(' ', microtime()));
    error_log($msg . ' -Mem: ' . memory_get_usage(true) . ' - ' . sprintf("%.4f", ($end - $start)) . " seconds");
}

function getNameSpaces()
{
    $namespaces=array();
    foreach(get_declared_classes() as $name) {
        if(preg_match_all("@[^\\\]+(?=\\\)@iU", $name, $matches)) {
            $matches = $matches[0];
            $parent =&$namespaces;
            while(count($matches)) {
                $match = array_shift($matches);
                if(!isset($parent[$match]) && count($matches))
                    $parent[$match] = array();
                $parent =&$parent[$match];

            }
        }
    }
    return $namespaces;
}