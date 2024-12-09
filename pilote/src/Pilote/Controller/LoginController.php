<?php

namespace Moowgly\Pilote\Controller;

use Moowgly\Lib\Controller\AbstractControllerPilote;
use Moowgly\Lib\Utils\ClientSilo;
use Zaphpa\Request;
use Zaphpa\Response;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\MailUtil;
use Moowgly\Pilote\Model\Guest;
use Moowgly\Pilote\Controller\GuestController;

/**
 * REST functions
 */
class LoginController extends AbstractControllerPilote
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function displayLogin(Request $req, Response $res)
    {

        $twig = getTwig();

        echo $twig->render('pages/login/login.twig');
    }

    public function displayInscription(Request $req, Response $res)
    {

        $twig = getTwig();

        echo $twig->render('pages/login/inscription.twig');
    }

public function login(Request $req, Response $res)
   {

       $req->data['password'] = UUID::md5touuid(md5($req->data['password']));

       $url = $_SERVER['PROJECT_SILO_URL'] . '/checkUserPwdEmail/v1/';

       $client = ClientSilo::getInstance();
       $response = $client->get($url, ['form_params' => $req->data]);

       $result = json_decode($response->getBody());

       if($result){

           $responseSf = new HttpFoundation\Response();
           // Création du cookie pgs / user
           $arrCookie = array('id_user' => $result->id, 'family_name' =>  $result->family_name,'first_name' =>  $result->first_name,'profile_guest' => $result->profile_guest,'profile_host' => $result->profile_host);

           $responseSf->headers->setCookie(new Cookie('moowgly', serialize($arrCookie), time() + (3600 * 48)));
           $responseSf->send();

           // Set session
           $targetUrl = "";

           $session = new Session();
           $session->set('id_user', $result->id);
           $session->set('family_name', $result->family_name);
           $session->set('first_name', $result->first_name);
           $session->set('email', $result->email);
           $session->set('profile_guest', $result->profile_guest);
           $session->set('profile_host', $result->profile_host);

           http_response_code(201);

           $done = array ('validation' => 'success', 'type' => 'alert-success' ,'message'=>'Login success !', "targetUrl" => "/moowgly/guest_dashboard");
           echo json_encode($done);

       }else{

           http_response_code(201);

           $done = array ('validation' => 'error', 'type' => 'alert-warning' ,'message'=>'Erreur d\'identifiants.
                   Ou utilisateur inactif. Cliquez ici pour recevoir le mail de validation.'); // Renvoyer le mail si pas reçu la premiere fois

           echo json_encode($done);

       }
   }

    public function disconnect(Request $req, Response $res)
    {
        $session = new Session();
        $session->remove('id_user');
        $session->remove('family_name');
        $session->remove('first_name');
        $session->remove('email');
        $session->remove('profile');
        $session->invalidate();

        $targetUrl = "";

        $done = array ("targetUrl" => $targetUrl);
        echo json_encode($done);
    }

    public function checkLoginAndRegister(Request $req, Response $res)
    {
        // $req->data = array( 'email' => 'gwendaldugue@gmail.com',
        //                     'password' => 'password',
        //                     'family_name' => 'Dugué',
        //                     'first_name' => '   Gwendal',
        //                     'birth_date' => '1986-08-01'
        //             );

        $req->data['id'] = UUID::strToUuid($req->data['email']);

        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data["id"];

        $client = ClientSilo::getInstance();
        $response = $client->get($url, ['form_params' => $req->data]);

        $result = json_decode($response->getBody());

        if($result == false){

            $req->data['password'] = UUID::md5touuid(md5($req->data['password']));
            $req->data['token'] = UUID::strToUuid($req->data['id']);

            $this->insert($req, $res); // insert to user table

            // Insert in guest Dynamodb
            $dyGuest = new Guest();
            $dyGuest = $dyGuest->parseDataForDy($req->data);

            $url = $_SERVER['PROJECT_SILO_URL'] . '/guest/v1/' . $dyGuest['id_guest'];

            $responseGuest = $client->post($url, ['form_params' => $dyGuest]);// insert to Guest table

            if($response->getStatusCode() == '200'){

                // Send mail with id user and token here
                // validationRegistration/$req->data['id']/$req->data['token']

                // To send mail out of the sandbox : https://console.aws.amazon.com/support/home?region=us-east-1#/case/create?issueType=service-limit-increase&limitType=service-code-ses

                $sendMail = new MailUtil();

                $message = "<strong>Nom :</strong> " . $req->data['family_name'] . "<br>
                            <strong>Prénom :</strong> " . $req->data['first_name'] . "<br>
                            <strong>Email :</strong> " . $req->data['email'] . "<br>
                            <strong>Lien de validation pour ce connecté :</strong>
                            <a href='http://localhost/moowgly/validationRegistration/" . $req->data['id'] . "/" . $req->data['token'] . " '>cliquez ici </a><br><br>";

                $sujet = "Valid your registration on Moowgly ! ";

                if ($sendMail->sendMail('no.reply.moowgly@gmail.com', array('no.reply.moowgly@gmail.com'), $sujet, $message) == 'sent') {
                    $done = array ('validation' => 'success', 'type' => 'alert-success' ,'message'=>'Vous allez recevoir un mail de validation.');
                }
                else {
                    // var_dump($sendMail->sendMail('no.reply.moowgly@gmail.com', array('no.reply.moowgly@gmail.com'), $sujet, $message));
                    $done = array ('validation' => 'error', 'type' => 'alert-error' ,'message'=> 'Mail non envoyé');
                }

            }else{

                $done = array ('validation' => 'error', 'type' => 'alert-error' ,'message'=> 'Insertion en bdd a échoué');
            }

            echo json_encode($done);

        }else{

            $done = array ('validation' => 'error', 'type' => 'alert-warning' ,'message'=>'Cette utilisateur existe déjà.');

            echo json_encode($done);
        }
    }

    public function validInscription(Request $req, Response $res)
    {
        // pour valider l'inscription, il faut savoir si le token et l'id existent dans la table user
        // puis mettre à jour le champ active à 1;
        $url = $_SERVER['PROJECT_SILO_URL'] . '/checkUserToken/v1/';

        $client = ClientSilo::getInstance();
        $response = $client->get($url, ['form_params' => $req->params]);

        if($response->getBody()->getContents() != false){
            // on doit update active à 1
            $req->data['id'] = $req->params['id'];
            $req->data['active'] = 1;

            if($this->update($req, $res) == '200' ){
                $done = array ('validation' => 'success', 'type' => 'alert-success' ,'message'=>'Vous pouvez vous connecter !');
                echo json_encode($done);
            }

        }else{

            $done = array ('validation' => 'error', 'type' => 'alert-warning' ,'message'=>'');
            echo json_encode($done);
        }
    }

    public function recoverPassword(Request $req, Response $res)
    {
        //check user exist
        $url = $_SERVER['PROJECT_SILO_URL'] . '/checkUserEmail/v1/';

        $client = ClientSilo::getInstance();
        $response = $client->get($url, ['form_params' => $req->data]);

        $result = json_decode($response->getBody());

        if($result){
            // create new password and send it
            $newPassword = uniqid();

            $req->data['id'] = UUID::strToUuid($req->data['email']);
            $req->data['password'] = UUID::md5touuid(md5($newPassword));

            if($this->update($req, $res) == '200' ){

                $sendMail = new MailUtil();

                $message = "<strong>Voici votre nouveau password : </strong> " . $newPassword . "<br>";

                $sujet = "Moowgly - new password ";


                if ($sendMail->sendMail('no.reply.moowgly@gmail.com', array('no.reply.moowgly@gmail.com'), $sujet, $message) == 'sent') {
                    $done = array ('validation' => 'success', 'type' => 'alert-success' ,'message'=>'A new password has been sent by mail.');
                }
                else {
                    // var_dump($sendMail->sendMail('no.reply.moowgly@gmail.com', array('no.reply.moowgly@gmail.com'), $sujet, $message));
                    $done = array ('validation' => 'error', 'type' => 'alert-error' ,'message'=> 'Mail error');
                }

            }else{

                $done = array ('validation' => 'success', 'type' => 'alert-success' ,'message'=>'error update password');
            }
            echo json_encode($done);
        }else{

            $done = array ('validation' => 'error', 'type' => 'alert-warning' ,'message'=>'error get user');
            echo json_encode($done);
        }
    }

    public function insert(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data['id'];

        $client = ClientSilo::getInstance();

		$response = $client->post($url, ['form_params' => $req->data]);

        return $response->getStatusCode();
    }

    public function update(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data['id'];
        $client = ClientSilo::getInstance();
		$response = $client->put($url, ['form_params' => $req->data]);

        return $response->getStatusCode();
    }

    public function delete(Request $req, Response $res)
    {
        $url = $_SERVER['PROJECT_SILO_URL'] . '/user/v1/' . $req->data['id'];
        $client = ClientSilo::getInstance();
		$response = $client->delete($url, ['form_params' => $req->data]);
    }

    public function testSendMail(Request $req, Response $res)
    {
        $sendMail = new MailUtil();

        $message = "test";

        $sujet = "Test !";

        var_dump($sendMail->sendMail('no.reply.moowgly@gmail.com', array('gwendaldugue@gmail.com'), $sujet, $message));

    }

    /**
     * Acces forbidden page display.
     */
    public static function interdit(Request $req, Response $res, $itemXML)
    {
        $twig = getTwig();
        echo $twig->render('interdit.html');
    }
}
