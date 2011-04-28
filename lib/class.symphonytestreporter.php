<?php
	
	class SymphonyTestReporter extends SimpleReporter {
		protected $fieldset;
		protected $list;
		
		public function __construct($character_set = 'UTF-8') {
			parent::__construct();
			
			$this->fieldset = new XMLElement('fieldset');
			$this->fieldset->setAttribute('class', 'settings');
			$this->fieldset->appendChild(new XMLElement('legend', __('Results')));
			$this->list = new XMLElement('dl');
			$this->list->setAttribute('class', 'stack');
		}
		
		public function getFieldset() {
			return $this->fieldset;
		}
		
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
			$item->setAttribute('class', 'message error');
			$item->setValue($message);
			$this->list->appendChild($item);
		}

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
			$item->setAttribute('class', 'message exception');
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
			$item->setValue($message);
			$this->list->appendChild($item);
		}
		
		public function paintFormattedMessage($message) {
			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message');
			$item->setValue($message);
			$this->list->appendChild($item);
		}

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
			$item->setValue($message);
			$this->list->appendChild($item);
		}

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
			$item->setValue($message);
			$this->list->appendChild($item);
		}
	}
	
?>