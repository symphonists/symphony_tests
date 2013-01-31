<?php

	/**
	* Login to Symphony
	*
	* Attempt to login then logout of the Symphony backend.
	*/
	class SymphonyTestLoginAndLogout extends WebTestCase {
		/**
		 * Username to login with.
		 */
		protected $username = '';

		/**
		 * Password to login with.
		 */
		protected $password = '';

		/**
		 * Attempt to login then logout.
		 */
		public function testLogin() {
			$this->get(SYMPHONY_URL . '/login/');

			$this->setField('username', $this->username);
			$this->setField('password', $this->password);
			$this->assertText('Symphony Login');

			$this->showRequest();
			$this->showHeaders();

			$this->clickSubmit('Login', array(
				'action[login]'	=> 'login'
			));

			$this->assertText('Log out');

			$this->showRequest();
			$this->showHeaders();

			$this->click('Log out');

			$this->assertNoText('Symphony Login');
			$this->showHeaders();
		}
	}

?>