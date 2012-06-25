<?php
	
	/**
	 * @package libs
	 */
	
	require_once TOOLKIT . '/class.administrationpage.php';
	
	/**
	 * Adds useful utilities, well not really. It just sets $root_url so
	 * that pages can build URLs.
	 */
	class SymphonyTestPage extends AdministrationPage {
		protected $root_url;
		
		public function __construct() {
			parent::__construct();
			
			$data = Symphony::Engine()->getPageCallback();
			
			$this->root_url = sprintf(
				'%s%s', SYMPHONY_URL, dirname($data['pageroot'])
			);
		}
	}
	
?>
