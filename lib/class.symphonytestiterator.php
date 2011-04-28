<?php
	
	class SymphonyTestIterator extends ArrayIterator {
		static protected $cache;
		
		public function __construct() {
			if (!isset(self::$cache)) {
				$paths = array(
					SYMPHONY . '/tests/test.*.php',
					WORKSPACE . '/tests/test.*.php'
				);
				$files = array();
				
				foreach (Symphony::ExtensionManager()->listInstalledHandles() as $handle) {
					$paths[] = sprintf(
						'%s/%s/tests/test.*.php',
						EXTENSIONS, $handle
					);
				}
				
				foreach ($paths as $path) {
					$found = glob($path, GLOB_NOSORT);

					if (empty($found)) continue;

					$files = array_merge($files, $found);
				}

				self::$cache = $files;

				parent::__construct($files);
			}

			else {
				parent::__construct(self::$cache);
			}
		}

		public function hasFileWithHandle($handle) {
			foreach ($this as $filter) {
				if (SymphonyTest::findHandleFromPath($filter) == $handle) return true;
			}

			return false;
		}
	}
	
?>