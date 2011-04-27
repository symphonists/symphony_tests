<?php
	
	class UnitTestExample extends UnitTest {
		public function about() {
			return (object)array(
				'name'			=> 'Example',
				'author'		=> (object)array(
					'name'			=> 'Rowan Lewis',
					'website'		=> 'http://rowanlewis.com/',
					'email'			=> 'me@rowanlewis.com'
				)
			);
		}
	}
	
?>