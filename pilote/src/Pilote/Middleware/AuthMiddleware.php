<?php

namespace Moowgly\Pilote\Middleware;

use Moowgly\Pilote\Model\UserConnected;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;

class AuthMiddleware extends \Zaphpa\BaseMiddleware
{
    public function preroute(&$req, &$res)
    {
        $session = new Session();

        // // Dev mock
        // $authOK = true;

        // if (!$authOK) {
        //     $res->add('Not authenticated');
        //     $res->send('401');
        // }
        // return;

        // var_dump($session->get('id_user'));

        // Test authentification
        if(is_null($session->get('id_user'))){

            // Liste toutes les parties du site ou nous devons etre logué

            $findme = array(
                        'dashboard',
                        'become-host',
                        'payment',
                        'guest_messages',
                        'guest_bookings_bookings',
                        'guest_reviews',
                        'guest_profile_profile',
                        'guest_profile_kids'
                    );

            $pageAuthNeeded = $this->strposa($_SERVER['REQUEST_URI'], $findme);

            if ($pageAuthNeeded !== false) {
                // echo 'We need to be authenticated before accessing this page<br>';
            }

        }else {

            $user = UserConnected::getInstance();
            $user->setUser($session->get('id_user'), $session->get('family_name'), $session->get('first_name'), $session->get('email'), $session->get('profile_guest'), $session->get('profile_host'));

            $findHostProfile = array(
                        'dashboard_dashboard',
                        'dashboard_messages_messages',
                        'dashboard_bookings_bookings',
                        'dashboard_offers_offers',
                        'dashboard_reviews_reviews',
                        'dashboard_profile_profile',
                        'dashboard_offers_offers',
                        'dashboard_offers_create-offer'
                    );

            $hostProfilNeeded = $this->strposa($_SERVER['REQUEST_URI'], $findHostProfile);

            if ($hostProfilNeeded !== false && ( $session->get('profile_host') == 'no') ){
                echo 'We need to be registered as host before accessing this page<br>';
            }
        }
    }

    /*
    *   function like strpos but search in an array
    */
    private function strposa($haystack, $needles=array(), $offset=0)
    {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
    }
}
