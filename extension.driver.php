<?php
	
	class Extension_Symphony_Tests extends Extension {
		public function about() {
			return array(
				'name'			=> 'Symphony Tests',
				'version'		=> '0.1',
				'release-date'	=> '2011-04-27',
				'author'		=> array(
					array(
						'name'			=> 'Rowan Lewis',
						'website'		=> 'http://rowanlewis.com/',
						'email'			=> 'me@rowanlewis.com'
					)
				)
			);
		}
		
		public function fetchNavigation() {
			return array(
				array(
					'location'	=> 'System',
					'name'		=> 'Tests',
					'link'		=> '/tests/'
				),
				array(
					'location'	=> 'System',
					'name'		=> 'Tests',
					'link'		=> '/test/',
					'visible'	=> 'no'
				)
			);
		}
	}

?>