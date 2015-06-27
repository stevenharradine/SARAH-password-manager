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
		public function getAllRecords ($USER_ID) {
			$records = array ();
			$data = mysql_query(<<<EOD
SELECT *
FROM `passman`
WHERE `last_updated` IN (
	SELECT MAX(`last_updated`)
	FROM `passman`
	WHERE `USER_ID` = $USER_ID GROUP BY `PASSMAN_ID`
);
EOD
			) or die(mysql_error());

			while ( ( $row = mysql_fetch_array( $data ) ) != null) {
				$PASSMAN_ID = $row['PASSMAN_ID'];
				$USER_ID = $row['USER_ID'];
				$site = $row['site'];
				$url = $row['url'];
				$username = $row['username'];
				$password = $row['password'];

				$records[] = new PasswordManagerRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password);
			}

			return $records;
		}

		public function updateRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password) {
			$sql  = 'INSERT INTO `sarah`.`passman` (`PASSMAN_ID`, `USER_ID`, `site`, `url`, `username`, `password`)';
			$sql .= "VALUES ('$PASSMAN_ID', '$USER_ID', '$site',  '$url',  '$username',  '$password');";
			
			return mysql_query($sql) or die(mysql_error());
		}

		public function addRecord ($USER_ID, $site, $url, $username, $password) {
			return mysql_query("INSERT INTO `sarah`.`passman` (`USER_ID`, `site`, `url`, `username`, `password`) VALUES ('$USER_ID', '$site',  '$url',  '$username',  '$password');") or die(mysql_error());
		}

		public function deleteRecord ($PASSMAN_ID, $USER_ID) {
			return mysql_query("DELETE FROM `sarah`.`passman` WHERE `PASSMAN_ID`='$PASSMAN_ID' AND `USER_ID`='$USER_ID';") or die(mysql_error());
		}

		public function getRecord ($PASSMAN_ID, $USER_ID) {
			$data = mysql_query("SELECT * FROM `passman` WHERE `last_updated` IN (SELECT MAX(`last_updated`) FROM `passman` WHERE `PASSMAN_ID`=$PASSMAN_ID AND `USER_ID` = $USER_ID GROUP BY `PASSMAN_ID`);") or die(mysql_error());
			$row = mysql_fetch_array( $data );

			$site = $row['site'];
			$url = $row['url'];
			$username = $row['username'];
			$password = $row['password'];

			return new PasswordManagerRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password);
		}
	}