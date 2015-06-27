<?php
	require_once '../../views/_secureHead.php';
	require_once '../../models/_add.php';
	require_once '../../models/_table.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$PASSMAN_ID = request_isset ('id');
		$site = request_isset ('site');
		$url = request_isset ('url');
		$username = request_isset ('username');
		$password = request_isset ('password');
		
		switch ($page_action) {
			case ('update_by_id') :
				$db_update_success = PasswordManager::updateRecord ($PASSMAN_ID, $USER_ID, $site, $url, $username, $password);
				break;
			case ('add_password') :
				$db_add_success = PasswordManager::addRecord ($USER_ID, $site, $url, $username, $password);
				break;
			case ('delete_by_id') :
				$db_delete_success = PasswordManager::deleteRecord ($PASSMAN_ID, $USER_ID);
				break;
		}

		$passman_records = PasswordManager::getAllRecords( $USER_ID );

		$page_title = 'PassMan';
		$alt_menu = getAddButton();

		// build add view
		$addView = new AddView ('Add', 'add_password');
		$addView->addRow ('site', 'Site');
		$addView->addRow ('url', 'URL');
		$addView->addRow ('username', 'Username');
		$addView->addRow ('password', 'Password');

		// build table view
		$tableView = new TableView ( array ('Site', 'Username', 'Password', '') );

		foreach ($passman_records as $record) {
			$tableView->addRow ( array ( TableView::createCell ('site', '<a href="' . $record->getUrl() . '" target="_blank">' . $record->getSite() . '</a>' ),
										 TableView::createCell ('username', $record->getUsername() ) ,
										 TableView::createCell ('password', '<span class="mask">************</span><span class="password-actual">' . $record->getPassword() . '</span>' ),
										 TableView::createEdit ($record->getPassmanId() )
									   )
							   );
		}

		// load views to be used in front end
		$views_to_load = array();
		$views_to_load[] = '../../views/_add.php';
		$views_to_load[] = '../../views/_table.php';
		
		include '../../views/_generic.php';
	}
?>
