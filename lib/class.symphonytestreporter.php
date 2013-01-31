<?php

	/**
	 * @package lib
	 */

	/**
	 * The SymphonyTestReporter class extends SimpleReporter to build a test case
	 * report using XMLElements instead of string concatenation.
	 */
	class SymphonyTestReporter extends SimpleReporter {
		/**
		 * The report parent element.
		 */
		protected $fieldset;

		/**
		 * The internal list of executed tests, errors, passes and skips.
		 */
		protected $list;

		/**
		 * Prepares a new fieldset.
		 * @access public
		 */
		public function __construct($character_set = 'UTF-8') {
			parent::__construct();

			$this->fieldset = new XMLElement('fieldset');
			$this->fieldset->setAttribute('class', 'settings');
			$this->fieldset->appendChild(new XMLElement('legend', __('Results')));
			$this->list = new XMLElement('dl');
			$this->list->setAttribute('class', 'stack');
		}

		/**
		 * Fetch the internal fieldset.
		 * @access public
		 */
		public function getFieldset() {
			return $this->fieldset;
		}

		/**
		 * The footer represents the end of reporting, all elements are linked here.
		 * @param string $test_name Name class of test.
		 * @access public
		 */
		public function paintFooter($test_name) {
			$failed = ($this->getFailCount() + $this->getExceptionCount() > 0);

			if ($this->list->getNumberOfChildren()) {
				$this->fieldset->appendChild($this->list);
			}

			$result = new XMLElement('p');
			$result->setValue(sprintf(
				'<strong>%d</strong> of <strong>%d</strong> test cases complete: <strong>%d</strong> passes, <strong>%d</strong> fails and <strong>%d</strong> exceptions.',

				$this->getTestCaseProgress(),
				$this->getTestCaseCount(),
				$this->getPassCount(),
				$this->getFailCount(),
				$this->getExceptionCount()
			));

			if ($failed) {
				$result->setAttribute('class', 'result failed');
			}

			else {
				$result->setAttribute('class', 'result success');
			}

			$this->fieldset->appendChild($result);
		}

		/**
		 * Add a PHP error message to the message list.
		 * @param string $message The message to output.
		 * @access public
		 */
		public function paintError($message) {
			parent::paintError($message);

			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);

			$item = new XMLElement('dt');
			$item->setAttribute('class', 'breadcrumb');
			$item->setValue(sprintf(
				'%s -&gt;',
				implode(' -&gt; ', $breadcrumb)
			));
			$this->list->appendChild($item);

			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message bad');
			$item->setValue($message);
			$this->list->appendChild($item);
		}

		/**
		 * Add an exception message to the message list.
		 * @param Exception $exception Used to generate the message.
		 * @access public
		 */
		public function paintException($exception) {
			parent::paintException($exception);

			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);

			$item = new XMLElement('dt');
			$item->setAttribute('class', 'breadcrumb');
			$item->setValue(sprintf(
				'%s -&gt;',
				implode(' -&gt; ', $breadcrumb)
			));
			$this->list->appendChild($item);

			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message bad');
			$item->setValue(sprintf(
				'Unexpected exception [%d] of type [%s] with message [%s] at [%s line %d]',
				$this->getExceptionCount(),
				get_class($exception),
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine()
			));
			$this->list->appendChild($item);
		}

		/**
		 * Add an assertion failure to the message list.
		 * @param string $message The message to output.
		 * @access public
		 */
		public function paintFail($message) {
			parent::paintFail($message);

			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);

			$item = new XMLElement('dt');
			$item->setAttribute('class', 'breadcrumb');
			$item->setValue(sprintf(
				'%s -&gt;',
				implode(' -&gt; ', $breadcrumb)
			));
			$this->list->appendChild($item);

			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message bad');
			$item->setValue(trim($message));
			$this->list->appendChild($item);
		}

		/**
		 * Add debuging messages to the message list.
		 * @param string $message The message to output.
		 * @access public
		 */
		public function paintFormattedMessage($message) {
			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message');
			$item->setValue(General::sanitize(trim($message)));
			$this->list->appendChild($item);
		}

		/**
		 * Add an assertion pass to the message list.
		 * @param string $message The message to output.
		 * @access public
		 */
		public function paintPass($message) {
			parent::paintPass($message);

			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);

			$item = new XMLElement('dt');
			$item->setAttribute('class', 'breadcrumb');
			$item->setValue(sprintf(
				'%s -&gt;',
				implode(' -&gt; ', $breadcrumb)
			));
			$this->list->appendChild($item);

			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message good');
			$item->setValue(trim($message));
			$this->list->appendChild($item);
		}

		/**
		 * Add an assertion skip to the message list.
		 * @param string $message The message to output.
		 * @access public
		 */
		public function paintSkip($message) {
			parent::paintSkip($message);

			$breadcrumb = $this->getTestList();
			array_shift($breadcrumb);

			$item = new XMLElement('dt');
			$item->setAttribute('class', 'breadcrumb');
			$item->setValue(sprintf(
				'%s -&gt;',
				implode(' -&gt; ', $breadcrumb)
			));
			$this->list->appendChild($item);

			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message skip');
			$item->setValue(trim($message));
			$this->list->appendChild($item);
		}
	}