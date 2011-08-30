<?php

	/**
	 * @package libs
	 */

	/**
	 * Fetches all available test cases.
	 */
	class SymphonyTestIterator extends ArrayIterator {
		/**
		 * Cached list of test cases.
		 * @access protected
		 * @static
		 */
		static protected $cache;

		/**
		 * Finds all test cases the first time it's run, after that it uses the cache.
		 */
		public function __construct() {
			if (!isset(self::$cache)) {
				$local = Symphony::ExtensionManager()->create('symphony_tests')->getExtensionDir();
				$paths = array(
					SYMPHONY . '/tests/test.*.php',
					$local . '/core-tests/test.*.php',
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

		/**
		 * Does this iterator contain a test case with the specified handle?
		 * @param string $handle The test case handle.
		 */
		public function hasFileWithHandle($handle) {
			foreach ($this as $filter) {
				if (SymphonyTest::findHandleFromPath($filter) == $handle) return true;
			}

			return false;
		}
	}

?>