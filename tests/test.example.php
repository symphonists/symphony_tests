<?php
	
	/**
	* Testing dude
	*
	* Does it work, does it work???
	*/
	class SymphonyTestExample extends WebTestCase {
		public function setUp() {
			
		}
		
		public function tearDown() {
			
		}
		
		public function testExample() {
			$this->assertEqual(true, false);
			trigger_error('Catastrophe');
			throw new Exception('wtf');
		}
	}
	
?>