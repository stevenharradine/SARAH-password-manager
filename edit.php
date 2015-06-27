<?php
	require_once '../../views/_secureHead.php';
	require_once $relative_base_path . 'models/edit.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$PASSMAN_ID = request_isset ('id');

		$passwordManager = new PasswordManager ();
		
		$record = $passwordManager->getRecord ($PASSMAN_ID, $USER_ID);

		$page_title = 'Edit | PassMan';

		// build edit view
		$editModel = new EditModel ('Edit', 'update_by_id', $PASSMAN_ID);
		$editModel->addRow ('site', 'Site', $record->getSite () );
		$editModel->addRow ('url', 'URL', $record->getUrl () );
		$editModel->addRow ('username', 'Username', $record->getUsername () );
		$editModel->addRow ('password', 'Password', $record->getPassword () );

		$views_to_load = array();
		$views_to_load[] = ' ' . EditView2::render($editModel);

		include $relative_base_path . 'views/_generic.php';
	}
?>