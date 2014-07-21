<?php
	require_once('phpmailer/phpmailer.inc.php');

	class Mail extends phpmailer{
		private $db;

		public function __construct(&$db = null){
			$this->db = $db == null ? new Database () : $db;
			$this->initPhpMailer(true);
		}
		
		//From will be abstracted through Accounts
		//FromName also will follow
		public function sendMail($from, $fromName, $to, $subject, $body) {
			$this->SetFrom($from, $fromName);
			$this->AddAddress($to);
			$this->Subject = $subject;
			$this->Body = $body;

			if(!$mail->Send()){
				return false;
			}
			return true;
		}

		/************************
		 * 		 HELPERS		*
		 ************************/

		private function initPhpMailer($debug = false) {
			$this->WordWrap = 75;
			$this->IsSMTP();
			$this->Host = "smtp.gmail.com";
			$this->SMTPAuth = true;
			$this->SMTPSecure = "ssl";
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465;
		}
	}
?>