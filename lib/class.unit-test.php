<?php
	
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/unit_tester.php';
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/web_tester.php';
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/reporter.php';
	
	class UnitTest {
		static public $instances;
		
		static public function exists($handle) {
			$iterator = new UnitTestsIterator();

			return $iterator->hasFileWithHandle($handle);
		}
		
		static public function readInformation($object) {
			$reflection = new ReflectionObject($object);
			$filename = $reflection->getFileName();
			$comment = self::stripComment($reflection->getDocComment());
			$info = (object)array(
				'name'			=> $reflection->getName(),
				'description'	=> null,
				'in-extension'	=> (strpos($filename, EXTENSIONS . '/') === 0),
				'in-symphony'	=> (strpos($filename, SYMPHONY . '/') === 0),
				'in-workspace'	=> (strpos($filename, WORKSPACE . '/') === 0)
			);
			
			if ($info->{'in-extension'}) {
				$info->extension = basename(dirname(dirname($filename)));
			}
			
			// Extract natural name:
			if (preg_match('%^[^\n]+%', $comment, $match)) {
				$info->name = $match[0];
				$comment = trim(substr($comment, strlen($match[0])));
			}
			
			// Extract description:
			if ($comment) {
				$info->description = $comment;
			}
			
			return $info;
		}
		
		static protected function stripComment($comment) {
			$trim_syntax = function($item) {
				return preg_replace('%^(/[*]{2}|\s*[*]/|\s*[*]+\s?)%', null, $item);
			};
			
			$lines = explode("\n", $comment);
			$lines = array_map($trim_syntax, $lines);
			$comment = implode("\n", $lines);
			
			return trim($comment);
		}
		
		static public function load($path) {
			if (!isset(self::$instances)) {
				self::$instances = array();
			}
			
			if (file_exists($path)) {
				$handle = self::findHandleFromPath($path);
				$class = self::findClassNameFromPath($path);
			}
			
			else {
				$handle = $path;
				$path = self::findPathFromHandle($path);
				$class = self::findClassNameFromPath($path);
			}
			
			if (!in_array($class, self::$instances)) {
				require_once $path;

				$instance = new $class;
				$instance->handle = $handle;
				
				if (!$instance instanceof SimpleTestCase) {
					throw new Exception('Unit test class must implement interface SymphonyTest.');
				}

				self::$instances[$class] = $instance;
			}
			
			return self::$instances[$class];
		}
		
		static public function findClassNameFromPath($path) {
			$handle = self::findHandleFromPath($path);
			$class = ucwords(str_replace('-', ' ', Lang::createHandle($handle)));
			$class = 'UnitTest' . str_replace(' ', null, $class);

			return $class;
		}
		
		static public function findHandleFromPath($path) {
			return preg_replace('%^test\.|\.php$%', null, basename($path));
		}
		
		static public function findPathFromHandle($handle) {
			foreach (new UnitTestsIterator() as $filter) {
				if (self::findHandleFromPath($filter) == $handle) return $filter;
			}

			return null;
		}
	}
	
	interface SymphonyTest {
		
	}
	
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
			$item->setAttribute('class', 'message failure');
			$item->setValue($message);
			$this->list->appendChild($item);
		}
		
		public function paintFormattedMessage($message) {
			$item = new XMLElement('dd');
			$item->setAttribute('class', 'message');
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