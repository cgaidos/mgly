<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Pilote\Model\Guest;
use Moowgly\Lib\Utils\UUID;
use Zaphpa\Request;
use Zaphpa\Response;
use Moowgly\Pilote\Model\UserConnected;

class GuestController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function dashboard(Request $req, Response $res)
    {
        $user = UserConnected::getInstance()->getUser();

        if(is_null($user['id_user'])){
            // return message
        }else{
            $guest = Guest::getGuest($user['id_user']);
        }

        $twig = getTwig();
        echo $twig->render('/guest/dashboard.twig');
    }

    public function messages(Request $req, Response $res)
    {
        $user = UserConnected::getInstance();

        $twig = getTwig();
        echo $twig->render('/guest/messages/messages.twig');
    }

    public function conversation(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/guest/messages/conversation.twig');
    }

    public function bookings(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/guest/bookings/bookings.twig');
    }

    public function reviews(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/guest/reviews/reviews.twig');
    }

    public function profile(Request $req, Response $res)
    {
        // $email will be a session var
        $email = "gwendaldugue@gmail.com";

        $id = UUID::strToUuid($email);

        $guest = Guest::getGuest($id);

        $twig = getTwig();

        echo $twig->render('/guest/profile/profile.twig');
    }

    public function kids(Request $req, Response $res)
    {
        // $guest = Guest::getGuest($session->get('id_user'));

        $twig = getTwig();
        echo $twig->render('/guest/profile/kids.twig');
    }

    public function guestCard(Request $req, Response $res)
    {
        $twig = getTwig();
        echo $twig->render('/guest-card/guest-card.twig');
    }

    /**
     * Data Manipulation.
     */

    public function insert(Request $req, Response $res)
    {
        // Bouchonner : créer un array qui va contenir les données du Guest pour tests

        // $email = "gwendaldugue@gmail.com";

        // $id = UUID::strToUuid($email);

        // // We are waiting for an array Like this one

        // $arrayData = array(
        //     "email" => $email,
        //     "family_name" => "Dugué",
        //     "first_name" => "Gwendal",
        //     "phone" => "06 69 02 46 65",
        //     "age" => 30,
        //     "photo" => "photo.png",
        //     "address" => [ "street" => "55 boulevard Sérurier", "zip_code" => "75019", "city" => "Paris", "country" => "France" ]
        // );

        // arrayData will be $req->data

        // var_dump($req->data);
        $dyGuest = new Guest();
        $dyGuest = $dyGuest->parseDataForDy($req->data);

        $url = $_SERVER['PROJECT_SILO_URL'] . '/guest/v1/' . $dyGuest['id_guest'];

        $client = ClientSilo::getInstance();
        $response = $client->post($url, ['form_params' => $dyGuest]);

        // echo $response->getStatusCode();
    }

    public function update(Request $req, Response $res)
    {
        // Bouchonner : créer un array qui va contenir les données du Guest pour tests

        $email = "gwendaldugue@gmail.com";

        $id = UUID::strToUuid($email);

        $keys = array( "keys" => ['id_guest' => $id]);

        // if a value of the form is empty, set the value to #dtdm

        // We are waiting for an array Like this one

        $arrayData = array(
            "email" => $email,
            "family_name" => "Dugué",
            "first_name" => "Gwendal",
            "phone" => "#dtdm",
            "kids" => "#dtdm",
            "age" => 55
        );

        // arrayData will be $req->data

        $arrayData =  $keys + $arrayData;

        $url = $_SERVER['PROJECT_SILO_URL'] . '/guest/v1/' . $id;

        $client = ClientSilo::getInstance();
        $response = $client->put($url, ['form_params' => $arrayData]);
    }

    // temporary function
    public function addKid(Request $req, Response $res)
    {
        // Bouchonner : créer un array qui va contenir les données du Guest pour tests

        $email = "emiliemoysson@gmail.com";

        $id = UUID::strToUuid($email);

        $keys = array( "keys" => ['id_guest' => $id]);

        // if a value of the form is empty, set #dtdm in the field,
        // Attention : if you just remove the value football it will remove automatically the data in the database

        /* To update / remove there is a diference between a simple array like this :
            array( "email" => $email, "kids" => "#dtdm" )  -> this will delete all the kids from guest

            and this part  :

            "kids" => [ [ 'first_name' => 'Marie', 'age' => 10, 'photo' => 'photo.png', 'activity' =>  [ 'sport' => ['tennis'] ] ]
            this will delete kid2 and delete 'football from kid1'
        */

        // We are waiting for an array Like this one

        $arrayData = array(
            "kids" => [
                        [ 'first_name' => 'Marie', 'age' => 15, 'photo' => 'photo.png', 'activity' =>  [ 'sport' => ['tennis'] ] ],

                        [ 'first_name' => 'Alexandre', 'age' => 10, 'photo' => 'photo.png', 'activity' =>  [ 'sport' => ['tennis', 'football'] ] ],
            ]
        );


        $arrayData =  $keys + $arrayData;

        $url = $_SERVER['PROJECT_SILO_URL'] . '/guest/v1/' . $id;

        $client = ClientSilo::getInstance();
        $response = $client->put($url, ['form_params' => $arrayData]);
        // $guest = json_decode($response->getBody());
        // var_dump($guest->address->M->country->S);

        var_dump($response->getStatusCode());
    }


    public function sendMail(Request $req, Response $res)
    {

    }

    /**
     * Acces forbidden page display.
     */
    public function delete(Request $req, Response $res)
    {
        $email = "emiliemoysson@gmail.com";

        // $arrayData will be $req->data
        $arrayData = array(
            "email" => $email
        );

        $id = UUID::strToUuid($email);

        $keys = array( "keys" => ['id_guest' => $id]);

        $arrayData =  $keys + $arrayData;

        // $url = $_SERVER['PROJECT_SILO_URL'] . '/guest/v1/' . $id;

        $client = ClientSilo::getInstance();
        // $response = $client->delete($url, ['form_params' => $arrayData]);

        $url = $_SERVER['PROJECT_SILO_URL'] . '/guest-delete/v1/' . $id;
        $response = $client->put($url, ['form_params' => $arrayData]);
    }

    /**
     * Acces forbidden page display.
     */
    public static function interdit($req, $res, $itemXML)
    {
        $twig = getTwig();
        echo $twig->render('interdit.html');
    }
}
