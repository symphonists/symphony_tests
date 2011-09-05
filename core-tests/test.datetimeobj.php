<?php

	/**
	* DateTimeObj tests
	*
	* Checks the DateTimeObj class to ensure Dates are interpreted correctly using the core Date field
	*/
	class SymphonyTestDateTimeObj extends UnitTestCase {

		protected $sample_dates = array(
			'1' => '2011-05-10T14:00',
			'2' => '2011-06-01T00:00',
			'3' => '2010-07-01T15:00',
			'4' => '1960-10-10T02:00',
			'5' => '2012-03-04T07:30'
		);

		public function setUp() {
			require_once TOOLKIT . '/fields/field.date.php';
		}

		public function testLaterThan() {
			$end = '2038-01-01 23:59:59';

			// `later than 2011` should return 5
			$string = 'later than 2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-12-31 23:59:59');
			$this->assertEqual($string['end'], $end);

			// `later than 2012` should return nothing
			$string = 'later than 2012';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2012-12-31 23:59:59');
			$this->assertEqual($string['end'], $end);

			// `later than 1960` should return 1,2,3,5
			$string = 'later than 1960';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '1960-12-31 23:59:59');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-05` should return 2,5
			$string = 'later than 2011-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-31 23:59:59');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-05-02` should return 1,2,5
			$string = 'later than 2011-05-02';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-02 23:59:59');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-05-10 1:00pm` should return 1,2,3,5
			$string = 'later than 2011-05-10 1:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 13:00:01');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-05-10 11:00am` should return 1,2,5
			$string = 'later than 2011-05-10 11:00am';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 11:00:01');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-05-10 2:00pm` should return 2,5
			$string = 'later than 2011-05-10 2:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 14:00:01');
			$this->assertEqual($string['end'], $end);

			// `later than 2011-06-01 8:00pm` should return 5
			$string = 'later than 2011-06-01 8:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-06-01 20:00:01');
			$this->assertEqual($string['end'], $end);

		}

		public function testEarlierThan() {
			$start = '0000-01-01';

			// `earlier than 2011` should return 3,4
			$string = 'earlier than 2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-01-01 00:00:00');

			// `earlier than 2012` should return 1,2,3,4
			$string = 'earlier than 2012';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2012-01-01 00:00:00');

			// `earlier than 1960` should return nothing
			$string = 'earlier than 1960';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '1960-01-01 00:00:00');

			// `earlier than 2011-05` should return 3,4
			$string = 'earlier than 2011-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-01 00:00:00');

			// `earlier than 2011-05-02` should return 3,4
			$string = 'earlier than 2011-05-02';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-02 00:00:00');

			// `earlier than 2011-05-10 11:00am` should return 1,2,3,4
			$string = 'earlier than 2011-05-10 11:00am';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-10 10:59:59');

			// `earlier than 2011-05-10 2:00pm` should return 3,4
			$string = 'earlier than 2011-05-10 2:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-10 13:59:59');

			// `earlier than 2011-06-01 8:00pm` should return 1,2,3,4
			$string = 'earlier than 2011-06-01 8:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-06-01 19:59:59');
		}

		public function testEqualToOrLaterThan() {
			$end = '2038-01-01 23:59:59';

			// `equal to or later than 2011` should return 1,2,5
			$string = 'equal to or later than 2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-01-01 00:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2012` should return 5
			$string = 'equal to or later than 2012';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2012-01-01 00:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 1960` should return 1,2,3,4,5
			$string = 'equal to or later than 1960';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '1960-01-01 00:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-05` should return 1,2,5
			$string = 'equal to or later than 2011-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-01 00:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-05-02` should return 1,2,5
			$string = 'equal to or later than 2011-05-02';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-02 00:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-05-10 1:00pm` should return 1,2,3,5
			$string = 'equal to or later than 2011-05-10 1:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 13:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-05-10 11:00am` should return 1,2,5
			$string = 'equal to or later than 2011-05-10 11:00am';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 11:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-05-10 2:00pm` should return 1,2,5
			$string = 'equal to or later than 2011-05-10 2:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-10 14:00:00');
			$this->assertEqual($string['end'], $end);

			// `equal to or later than 2011-06-01 8:00pm` should return 5
			$string = 'equal to or later than 2011-06-01 8:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-06-01 20:00:00');
			$this->assertEqual($string['end'], $end);

		}

		public function testEqualToOrEarlierThan() {
			$start = '0000-01-01';

			// `equal to or earlier than 2011` should return 1,2,3,4
			$string = 'equal to or earlier than 2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-12-31 23:59:59');

			// `equal to or earlier than 2012` should return 1,2,3,4,5
			$string = 'equal to or earlier than 2012';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2012-12-31 23:59:59');

			// `equal to or earlier than 1960` should return 4
			$string = 'equal to or earlier than 1960';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '1960-12-31 23:59:59');

			// `equal to or earlier than 2011-05` should return 1,3,4
			$string = 'equal to or earlier than 2011-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-31 23:59:59');

			// `equal to or earlier than 2011-05-02` should return 3,4
			$string = 'equal to or earlier than 2011-05-02';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-02 23:59:59');

			// `equal to or earlier than 2011-05-10 1:00pm` should return 4
			$string = 'equal to or earlier than 2011-05-10 1:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-10 13:00:00');

			// `equal to or earlier than 2011-05-10 11:00am` should return 3,4
			$string = 'equal to or earlier than 2011-05-10 11:00am';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-10 11:00:00');

			// `equal to or earlier than 2011-05-10 2:00pm` should return 1,3,4
			$string = 'equal to or earlier than 2011-05-10 2:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-05-10 14:00:00');

			// `equal to or earlier than 2011-06-01 8:00pm` should return 1,2,3,4
			$string = 'equal to or earlier than 2011-06-01 8:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], $start);
			$this->assertEqual($string['end'], '2011-06-01 20:00:00');
		}

		public function testTo() {
			// `2011 to 2012` should return 1,2
			$string = '2011 to 2012';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-01-01 00:00:00');
			$this->assertEqual($string['end'], '2012-12-31 23:59:59');

			// `2011-05 to 2012-05` should return 1,2,5
			$string = '2011-05 to 2012-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-01 00:00:00');
			$this->assertEqual($string['end'], '2012-05-31 23:59:59');

			// `2011-05-01 to 2011-06-10` should return 1,2
			$string = '2011-05-01 to 2011-06-10';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-05-01 00:00:00');
			$this->assertEqual($string['end'], '2011-06-10 23:59:59');

			// `2010-06-01 2:00pm to 2011-06-01 11:00pm` should return nothing
			$string = '2010-06-01 2:00pm to 2011-06-01 11:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2010-06-01 14:00:00');
			$this->assertEqual($string['end'], '2011-06-01 23:00:00');

			// `2010-05-01 11:00am to 2011` should return 1,2,3
			$string = '2010-05-01 11:00am to 2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2010-05-01 11:00:00');
			$this->assertEqual($string['end'], '2011-12-31 23:59:59');

			// `2010-05-01 11:00am to 2011-05` should return 1,3
			$string = '2010-05-01 11:00am to 2011-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2010-05-01 11:00:00');
			$this->assertEqual($string['end'], '2011-05-31 23:59:59');

			// `2010-06-01 12:00am to 2010-06-01 9:30pm` should return 2
			$string = '2010-06-01 12:00am to 2010-06-01 9:30pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2010-06-01 00:00:00');
			$this->assertEqual($string['end'], '2010-06-01 21:30:00');
		}

		public function testDirect() {
			// `2011` should return 1,2
			$string = '2011';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-01-01 00:00:00');
			$this->assertEqual($string['end'], '2011-12-31 23:59:59');

			// `2010-05` should return nothing
			$string = '2010-05';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2010-05-01 00:00:00');
			$this->assertEqual($string['end'], '2010-05-31 23:59:59');

			// `2011-06-01` should return 2
			$string = '2011-06-01';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-06-01 00:00:00');
			$this->assertEqual($string['end'], '2011-06-01 23:59:59');

			// `2011-06-10 2:00pm` should return nothing
			$string = '2011-06-10 2:00pm';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '2011-06-10 14:00:00');
			$this->assertEqual($string['end'], '2011-06-10 14:00:00');

			// `1960-10-10 2:00am` should return 4
			$string = '1960-10-10 2:00am';
			$result = fieldDate::parseFilter($string);

			$this->assertEqual($string['start'], '1960-10-10 02:00:00');
			$this->assertEqual($string['end'], '1960-10-10 02:00:00');
		}
	}

?>