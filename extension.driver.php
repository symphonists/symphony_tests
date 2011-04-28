<?php
	
	class Extension_Unit_Tests extends Extension {
		public function about() {
			return array(
				'name'			=> 'Unit Tests',
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
					'location'	=> 'Blueprints',
					'name'		=> 'Unit Tests',
					'link'		=> '/tests/'
				),
				array(
					'location'	=> 'Blueprints',
					'name'		=> 'Unit Tests',
					'link'		=> '/test/',
					'visible'	=> 'no'
				)
			);
		}
	}

?>