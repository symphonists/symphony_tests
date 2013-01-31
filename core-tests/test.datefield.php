<?php

	/**
	* DateField tests
	*
	* Tests to simulate the Date field.
	*/
	class SymphonyTestDateField extends UnitTestCase {

		public $field = null;

		public function setUp() {
			require_once TOOLKIT . '/class.field.php';
			require_once TOOLKIT . '/fields/field.date.php';

			$this->field = new fieldDate();
		}

		public function testcheckPostFieldData() {
			DateTimeObj::setSettings(array(
				'time_format' => 'g:i a',
				'date_format' => 'm/d/Y',
				'datetime_separator' => ' ',
				'timezone' => 'Australia/Brisbane',
			));

			$message = '';

			// Date in pattern expected by the Settings
			$this->assertEqual(Field::__OK__, $this->field->checkPostFieldData('10/26/2011 9:48 pm', $message));

			// Date in a valid, but different pattern to the Settings
			$this->assertEqual(Field::__OK__, $this->field->checkPostFieldData('2011-10-26 21:48', $message));

			// Date in an invalid pattern (according to PHP anyway)
			$this->assertEqual(Field::__INVALID_FIELDS__, $this->field->checkPostFieldData('Not a date', $message));
		}

		public function testprocessRawFieldDataConfigFormat() {
			DateTimeObj::setSettings(array(
				'time_format' => 'g:i a',
				'date_format' => 'm/d/Y',
				'datetime_separator' => ' ',
				'timezone' => 'Australia/Brisbane',
			));

			$input = '10/26/2011 9:48 pm';
			$status = null;

			$result = $this->field->processRawFieldData($input, $status);

			$this->assertEqual($result['value'], '2011-10-26T21:48:00+10:00');
			$this->assertEqual($result['date'], '2011-10-26 11:48:00');
		}

		public function testprocessRawFieldDataDifferentFormat() {
			DateTimeObj::setSettings(array(
				'time_format' => 'g:i a',
				'date_format' => 'm/d/Y',
				'datetime_separator' => ' ',
				'timezone' => 'Australia/Brisbane',
			));

			$input = '2011-10-26 21:48';
			$status = null;

			$result = $this->field->processRawFieldData($input, $status);

			$this->assertEqual($result['value'], '2011-10-26T21:48:00+10:00');
			$this->assertEqual($result['date'], '2011-10-26 11:48:00');
		}

		public function testprocessRawFieldDataDifferentTimezone() {
			DateTimeObj::setSettings(array(
				'time_format' => 'g:i a',
				'date_format' => 'm/d/Y',
				'datetime_separator' => ' ',
				'timezone' => 'Australia/Brisbane',
			));

			$input = '2011-10-26T21:48:00Z';
			$status = null;

			$result = $this->field->processRawFieldData($input, $status);

			$this->assertEqual($result['value'], '2011-10-27T07:48:00+10:00');
			$this->assertEqual($result['date'], '2011-10-26 21:48:00');

			return $result;
		}

	}

?>