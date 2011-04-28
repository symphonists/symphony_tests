<?php
	
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/unit_tester.php';
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/web_tester.php';
	require_once EXTENSIONS . '/unit_tests/lib/simpletest/reporter.php';
	require_once EXTENSIONS . '/unit_tests/lib/class.symphonytestiterator.php';
	require_once EXTENSIONS . '/unit_tests/lib/class.symphonytestpage.php';
	require_once EXTENSIONS . '/unit_tests/lib/class.symphonytestreporter.php';
	
	class SymphonyTest {
		static public $instances;
		
		static public function exists($handle) {
			$iterator = new SymphonyTestIterator();

			return $iterator->hasFileWithHandle($handle);
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
			$class = 'SymphonyTest' . str_replace(' ', null, $class);

			return $class;
		}
		
		static public function findHandleFromPath($path) {
			return preg_replace('%^test\.|\.php$%', null, basename($path));
		}
		
		static public function findPathFromHandle($handle) {
			foreach (new SymphonyTestIterator() as $filter) {
				if (self::findHandleFromPath($filter) == $handle) return $filter;
			}

			return null;
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
	}
	
?>