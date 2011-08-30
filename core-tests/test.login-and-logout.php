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
			$this->assertText('Login');

			$this->showRequest();
			$this->showHeaders();

			$this->clickSubmit('Login', array(
				'action[login]'	=> 'login'
			));

			$this->assertText('Logout');

			$this->showRequest();
			$this->showHeaders();

			$this->click('Logout');

			$this->assertNoText('Login');
			$this->showHeaders();
		}
	}

?>