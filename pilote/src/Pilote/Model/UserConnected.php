<?php
namespace Moowgly\Pilote\Model;

final class UserConnected
{
	private $id_user;
	private $family_name;
	private $first_name;
	private $email;
	private $profile_guest;
	private $profile_host;
	
	private function __construct()
	{
	}

	public static function getInstance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * @return array avec toutes les infortions du contributeur authentifié
	 */
	public function getUser()
	{
		return array(
			'id_user' => $this->id_user,
			'family_name' => $this->family_name,
			'first_name' => $this->first_name,
			'email' => $this->email,
			'profile_guest' => $this->profile_guest,
			'profile_host' => $this->profile_host
		);
	}
	
	/**
	 * Set toutes les informations d'utilisateur authentifié
	 * @param string $id_user
	 * @param string $family_name
	 * @param string $first_name
	 * @param string $email
	 * @param string $profile_guest
	 * @param string $profile_host
	 */
	public function setUser($id_user, $family_name, $first_name, $email, $profile_guest, $profile_host){
		
		$this->id_user = $id_user;
		$this->family_name = $family_name;
		$this->first_name = $first_name;
		$this->email = $email;
		$this->profile_guest = $profile_guest;
		$this->profile_host = $profile_host;
	}
	
}