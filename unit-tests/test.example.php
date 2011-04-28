<?php
	
	/**
	* Testing dude
	*
	* Fuck this is so boring!
	*/
	class UnitTestExample extends WebTestCase {
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