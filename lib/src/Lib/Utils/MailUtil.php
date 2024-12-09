<?php
namespace Moowgly\Lib\Utils;

use Aws\Ses\SesClient;
/**
 * Classe pour gerer les mails
 */
class MailUtil {
	/**
	 * Envoi d'un mail
	 *
	 * @param $from : Expediteur
	 * @param $to : Destinataire
	 * @param $subject : Sujet
	 * @param $message : Message
	 * @param $files : Pièce-jointes
	 */
	public static function sendRawEmail($from, $to, $subject, $message, $filePaths = array()) {
		$amazonKeyOut = $_SERVER['AMAZONSES_KEY'];
		$amazonSecretOut = $_SERVER['AMAZONSES_SECRET'];
		
		$messageText = implode ( $message );
		$messageHTML = implode ( '<br \>', $message );
		// création du msg
		$msg = "To: " . $to . "\n";
		$msg .= "From: " . $from . "\n";
		$msg .= "Subject: " . $subject . "\n";
		$msg .= "MIME-Version: 1.0\n";
		$msg .= "Content-Type: multipart/mixed;\n";
		$boundary = uniqid ( "_Part_" . time (), true ); // random unique string
		$boundary2 = uniqid ( "_Part2_" . time (), true ); // random unique string
		$msg .= " boundary=\"$boundary\"\n";
		$msg .= "\n";
		// le body
		$msg .= "--$boundary\n";
		$msg .= "Content-Type: multipart/alternative;\n";
		$msg .= " boundary=\"$boundary2\"\n";
		$msg .= "\n";
		$msg .= "--$boundary2\n";
		// d'abord la partie texte
		$msg .= "Content-Type: text/plain; charset=utf-8\n";
		$msg .= "Content-Transfer-Encoding: 7bit\n";
		$msg .= "\n";
		$msg .= $messageText; // remove any HTML tags
		$msg .= "\n";
		// puis HTML
		$msg .= "--$boundary2\n";
		$msg .= "Content-Type: text/html; charset=utf-8\n";
		$msg .= "Content-Transfer-Encoding: 7bit\n";
		$msg .= "\n";
		$msg .= $messageHTML;
		$msg .= "\n";
		$msg .= "--$boundary2--\n";
		// ajout des pièce-jointes
		$count = count ( $filePaths );
		foreach ( $filePaths as $filePath ) {
			$msg .= "\n";
			$msg .= "--$boundary\n";
			$msg .= "Content-Transfer-Encoding: base64\n";
			$filename = basename ( $filePath );
			$msg .= "Content-Type: application/octet-stream; name=" . $filename . ";\n";
			$msg .= "Content-Disposition: attachment; filename=" . $filename . ";\n";
			$msg .= "\n";
			$msg .= base64_encode ( file_get_contents ( $filePath ) );
			$msg .= "\n--$boundary";
		}

		// fermeture email
		$msg .= "--\n";
		try {

			$client = SesClient::factory(array(

				'region' => 'eu-west-1',
				'version' => 'latest',
				'credentials' => array(
					    'key' => $amazonKeyOut,
						'secret' => $amazonSecretOut,
					)
			));
			
			$client->sendEmail ( array (
					"Source" => $from,
					'Destination' => array (
							'ToAddresses' => array (
									$to
							)
					),
					'Message' => array (
							'Data' => base64_encode ( $msg )
					)
			) );
			return 'sent';
		} catch ( \Exception $e ) {
			error_log($e->getMessage ());
			return $e->getMessage ();
		}
	}


	public static function sendMail($from, $to, $subject, $message)
	{	
		$amazonKeyOut = $_SERVER['AMAZONSES_KEY'];
		$amazonSecretOut = $_SERVER['AMAZONSES_SECRET'];
		
	    $messageText = $message;
	    $messageHTML = $message;
	    try {
	       $client = SesClient::factory([
	          	'region'    => 'eu-west-1',
	          	'version' => 'latest',
				'credentials' => array(
					    'key' => $amazonKeyOut,
						'secret' => $amazonSecretOut,
					)
	           ]);
	       $client->sendEmail(
	           array(
	               "Source" => $from,
	               'Destination' => array(
	                   'ToAddresses' => $to
	               ),
	               'Message' => array(
	                   'Subject' => array(
	                       'Data' => $subject,
	                       'Charset' => 'UTF-8',
	                   ),
	                   'Body' => array(
	                       'Text' => array(
	                           'Data' => $messageText,
	                           'Charset' => 'UTF-8'
	                       ),
	                       'Html' => array(
	                           'Data' => $messageHTML,
	                           'Charset' => 'UTF-8'
	                       )
	                   )
	               )
	           )
	       );
	       return 'sent';
	   } catch (\Exception $e) {
	       return $e->getMessage();
	   }

	}
	
	public static function sendMail2($from, $to, $subject, $message) {
	    
		$amazonKeyOut = $_SERVER['AMAZONSES_KEY'];
		$amazonSecretOut = $_SERVER['AMAZONSES_SECRET'];
		
		$messageText = implode ( $message );
		$messageHTML = implode ( '<br \>', $message );
		// création du msg
		$msg  = "To: " . $to . "\n";
		$msg .= "From: " . $from . "\n";
		$msg .= "Subject: =?utf-8?Q?" . $subject . "?=\n";
		$msg .= "MIME-Version: 1.0\n";
		$boundary = uniqid (time (), true ); // random unique string
		$msg .= "Content-Type: multipart/alternative;";
		$msg .= " boundary=\"$boundary\"\n";
		$msg .= "\n";
		$msg .= "--$boundary\n";
		// d'abord la partie texte
		$msg .= "Content-Type: text/plain; charset=utf-8\n";
		$msg .= "Content-Transfer-Encoding: quoted-printable\n";
		$msg .= "\n";
		$msg .= $messageText; // remove any HTML tags
		$msg .= "\n";
		// puis HTML
		$msg .= "--$boundary\n";
		$msg .= "Content-Type: text/html; charset=utf-8\n";
		$msg .= "Content-Transfer-Encoding: quoted-printable\n";
		$msg .= "\n";
		$msg .= $messageHTML;
		$msg .= "\n";
		$msg .= "--$boundary";
		// fermeture email
		$msg .= "--\n";
		try {
			$client = SesClient::factory([
	          	'region'    => 'eu-west-1',
	          	'version' => 'latest',
				'credentials' => array(
					    'key' => $amazonKeyOut,
						'secret' => $amazonSecretOut,
					)
	           ]);
			$client->sendRawEmail ( array (
					"Source" => $from,
					'Destination' => array (
							'ToAddresses' => array (
									$to
							)
					),
					'RawMessage' => array (
							'Data' => base64_encode ( $msg )
					)
			) );
			return 'sent';
		} catch ( \Exception $e ) {
			error_log($e->getMessage ());
			return $e->getMessage ();
		}
	}
}