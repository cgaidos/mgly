<?php

namespace Moowgly\Pilote\Middleware;

class FormValid
{
	private $redirect_url = '/moowgly';
	private $data = array();
	private $error_args = '';
	public $msg_error = '';
	public $error = false;

	/**
	 * Constructor.
	 */
	public function __construct ($data = array()){
		$this->redirect_url = '/moowgly';
		$this->msg_error = '';
		$this->data = $data;
		$this->error_args = '';
		$this->error = false;
	}

	public function verifForm($requiredInput){
		foreach ($requiredInput as $tempInput){
			$this->verifInput($tempInput);
		}
	}

	public function setRedirectUrl($url){
		$this->redirect_url = '/moowgly/' . $url;
	}

	public function getDataResponse(){
		return [
				'redirect_url' => $this->redirect_url,
				'error_args' => $this->error_args,
				'msg_error' => $this->msg_error,
				'error' => $this->error,
		];
	}

	public function getData(){
		return $this->data;
	}

	public function addData($data){
		foreach($data as $key => $value) {
			$this->data[$key] = $value;
		}
	}

	private function verifInput($inputName){
		if (!isset($this->data[$inputName]) || $this->data[$inputName] == '' || empty($this->data[$inputName])){
			$this->error_args[] = $inputName;
			$this->error = true;
			$this->msg_error = 'incomplete';
		}
	}

}