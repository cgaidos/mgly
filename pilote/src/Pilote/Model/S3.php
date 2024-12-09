<?php

namespace Moowgly\Pilote\Model;

use Aws\S3\S3Client;
use Moowgly\Lib\Utils\ClientSilo;
use Moowgly\Lib\Utils\UUID;
use Moowgly\Lib\Utils\ImageUtil;
/**
 * REST functions
 */
class S3
{
	private static $_instance;
	private static $_bucket;
	private static $_key;
	private static $_secret;
	private static $_clientS3;
	//     $_key
	/**
	* Constructor.
	*/
	public function __construct()
	{
		self::$_bucket  = $_SERVER['AMAZONS3_BUCKET'];
		self::$_key     = $_SERVER['AMAZONS3_KEY'];
		self::$_secret  = $_SERVER['AMAZONS3_SECRET'];
		self::$_clientS3 = S3Client::factory(array(
				'key' =>    self::$_key,
				'secret' => self::$_secret,
		));
	}

	public static function getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new S3();
		}

		return self::$_instance;
	}

	//     public static function get()
	//     {
	//         $client = ClientSilo::getInstance();

	//         $url = 'http://cdn.mos.techradar.com/art/mobile_phones/Google/nexus5/Nexus%205%20Review/nexus-5-review-52-970-81.jpg';

	//         return $client->get($url);
	//     }

	//     public static function create()
	//     {
	//     }

		public static function get($file_name)
		{
			$file_result = null;
			$file_name = str_replace('http://', '', $file_name);
			if ($file_name != '') {
				if(self::$_clientS3->doesObjectExist(self::$_bucket, $file_name)) {
					$file_result = self::$_clientS3->getObject(array(
							'Bucket' => self::$_bucket,
							'Key' => $file_name
					));
				}
			}
			return $file_result;
		}

		public static function set($file_name, $data) {
			error_log(self::$_bucket);
			error_log($file_name);
			error_log($data);
			$file_result = self::$_clientS3->putObject(array(
					'Bucket' => self::$_bucket,
					'Key' =>    $file_name,
					'Body' =>   $data,
					'ContentType' => 'image/jpeg'
			));

			return $file_result;
		}

		public static function setMultiple($files) {

			$ids = [];
			foreach($files as $key => $obj) {
				if($obj['tmp_name'] == '') continue;

				$ids[$key] = UUID::uuid();
				self::set(end($ids), file_get_contents($obj['tmp_name']));
			}
			return $ids;
		}
		
		public static function delete($key) {
			error_log(self::$_bucket);
			error_log($key);
			$fileExists = self::$s3Client->doesObjectExist ( self::$_bucket, $key);
			if ($fileExists) {
				self::$s3Client->deleteObject ( array (
						'Bucket' => self::$_bucket,
						'Key' => $key
				) );
			}else
				return false;
			return true;
		}
		
		
		public function getThumb($file_name, $size = null) {
			// $file_name = str_replace('http://', '', $file_name);
			$client = self::$_clientS3;
			try {
				if ($size !== null) {
					$fileNameThumb = $file_name . '-thumbs=' . $size;
					// L'image existe à la bonne taille, on la retourne
					if ($client->doesObjectExist ( self::$_bucket, $fileNameThumb )) {
						$data = $client->getObject ( array (
								'Bucket' => self::$_bucket,
								'Key' => $fileNameThumb
						) );
						$data = $data ['Body'];
						// Sinon, on récupère l'image à la taille d'origine et on la retraite
					} else {
						$result = $client->getObject ( array (
								'Bucket' => self::$_bucket,
								'Key' => $file_name
						) );
						// Les miniature n'existent pas, on les fabrique.
						if (strpos ( $result ['ContentType'], 'image' ) !== false) {
							header ( 'Content-type: image/jpeg' );
							$data = ImageUtil::compressAndResizeFromS3 ( $result ['Body'], $fileNameThumb, 90, $size, $size );
							$client->putObject ( array (
									'Bucket' => self::$_bucket,
									'Key' => $fileNameThumb,
									'Body' => $data,
									'ContentType' => 'image/jpeg'
							) );
						} else {
							$data = ImageUtil::createBlankImage ();
						}
					}
				} else {
					$result = $client->getObject ( array (
							'Bucket' => self::$_bucket,
							'Key' => $file_name
					) );
					$data = $result ['Body'];
				}
			} catch ( \Exception $e ) {
				$data = ImageUtil::createBlankImage ();
			}
		
			return array (
					'Body' => $data,
					'ContentType' => 'image/jpeg'
			);
		}

	}