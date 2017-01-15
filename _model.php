<?php
	class PasswordManagerRecord {
		private $PASSMAN_ID;
		private $USER_ID;
		private $site;
		private $url;
		private $username;
		private $password;

		public function __construct ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password) {
			$this->PASSMAN_ID = $PASSMAN_ID;
			$this->USER_ID	= $USER_ID;
			$this->site = $site;
			$this->url = $url;
			$this->username = $username;
			$this->password = $password;
		}

		public function getPassmanId () {
			return $this->PASSMAN_ID;
		}
		public function getUserId () {
			return $this->USER_ID;
		}
		public function getSite () {
			return $this->site;
		}
		public function getUrl () {
			return $this->url;
		}
		public function getUsername () {
			return $this->username;
		}
		public function getPassword () {
			return $this->password;
		}
	}

	class PasswordManager {
		public function get_link () {
			global $DB_ADDRESS;
			global $DB_USER;
			global $DB_PASS;
			global $DB_NAME;

			return $link = DB_Connect($DB_ADDRESS, $DB_USER, $DB_PASS, $DB_NAME);
		}
		public function getAllRecords ($USER_ID) {
			global $sessionManager;
			$link = PasswordManager::get_link();
			$USER_ID = $sessionManager->getUserId();

			$records = array ();
			$sql = <<<EOD
SELECT
	*
FROM
	`passman`
WHERE
	`last_updated`
IN (
	SELECT
		MAX(`last_updated`)
	FROM
		`passman`
	WHERE
		`USER_ID` = $USER_ID
	GROUP BY
		`PASSMAN_ID`
);
EOD;
			$data = $link->query($sql);

			if ($result = $link->query($sql)) {
				while ( $row = (array) $result->fetch_object() ) {
					$PASSMAN_ID = $row['PASSMAN_ID'];
					$USER_ID = $row['USER_ID'];
					$site = $row['site'];
					$url = $row['url'];
					$username = $row['username'];
					$password = $row['password'];

					$records[] = new PasswordManagerRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password);
				}
			}

			return $records;
		}

		public function updateRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password) {
			global $sessionManager;
			$link = PasswordManager::get_link();
			$USER_ID = $sessionManager->getUserId();

			$sql  = <<<EOD
INSERT INTO
	`sarah`.`passman` (
		`PASSMAN_ID`,
		`USER_ID`,
		`site`,
		`url`,
		`username`,
		`password`
	) VALUES (
		'$PASSMAN_ID',
		'$USER_ID',
		'$site',
		'$url',
		'$username',
		'$password'
	);
EOD;

			$result = $link->query($sql);
			
			return  $result;
		}

		public function addRecord ($USER_ID, $site, $url, $username, $password) {
			global $sessionManager;
			$link = PasswordManager::get_link();
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
INSERT INTO
	`sarah`.`passman` (
		`USER_ID`,
		`site`,
		`url`,
		`username`,
		`password`
	) VALUES (
		'$USER_ID',
		'$site',
		'$url',
		'$username',
		'$password'
	);
EOD;

			$result = $link->query($sql);

			return $result;
		}

		public function deleteRecord ($PASSMAN_ID, $USER_ID) {
			global $sessionManager;
			$link = PasswordManager::get_link();
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
DELETE FROM
	`sarah`.`passman`
WHERE
	`PASSMAN_ID`='$PASSMAN_ID'
		AND 
	`USER_ID`='$USER_ID';
EOD;
			
			$result = $link->query($sql);

			return $result;
		}

		public function getRecord ($PASSMAN_ID, $USER_ID) {
			global $sessionManager;
			$link = PasswordManager::get_link();
			$USER_ID = $sessionManager->getUserId();

			$sql = <<<EOD
SELECT
	*
FROM
	`passman`
WHERE
	`last_updated`
IN (
	SELECT
		MAX(`last_updated`)
	FROM
		`passman`
	WHERE
		`PASSMAN_ID`=$PASSMAN_ID
			AND
		`USER_ID` = $USER_ID
	GROUP BY
		`PASSMAN_ID`
);
EOD;

			if ($result = $link->query($sql)) {
				$row = (array) $result->fetch_object();

				$site = $row['site'];
				$url = $row['url'];
				$username = $row['username'];
				$password = $row['password'];

				return new PasswordManagerRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password);
			} else {
				return NULL;
			}

		}
	}