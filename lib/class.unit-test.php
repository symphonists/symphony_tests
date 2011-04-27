<?php
	
	class UnitTest {
		static public $instances;
		
		static public function exists($handle) {
			$iterator = new ImageFiltersIterator();

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
				
				// Belongs to an extension:
				if (strpos($path, EXTENSIONS) === 0) {
					$extension = basename(dirname(dirname($path)));
					$instance->extension = Symphony::ExtensionManager()->create($extension);
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
			foreach (new ImageFiltersIterator() as $filter) {
				if (self::findHandleFromPath($filter) == $handle) return $filter;
			}

			return null;
		}
		
		public $handle;
		public $extension;
		
		public function about() {
			return (object)array();
		}

		public function getFileName() {
			$reflection = new ReflectionObject($this);
			
			return $reflection->getFileName();
		}
	}
	
?>