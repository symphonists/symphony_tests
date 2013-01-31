<?php

	/**
	 * @package lib
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
		public function __construct(array $paths = null) {
			$key = (
				is_array($paths) && empty($paths) === false
					? implode(':', $paths)
					: 'all'
			);

			if (isset(self::$cache) === false) {
				self::$cache = array();
			}

			if (isset(self::$cache[$key]) === false) {
				$files = array();

				if ($paths === null) {
					$local = ExtensionManager::create('symphony_tests')->getExtensionDir();
					$paths = array(
						$local . '/core-tests',
						WORKSPACE . '/tests'
					);

					foreach (ExtensionManager::listInstalledHandles() as $handle) {
						$paths[] = sprintf(
							'%s/%s/tests',
							EXTENSIONS, $handle
						);
					}
				}

				foreach ($paths as $path) {
					$found = glob($path . '/test.*.php', GLOB_NOSORT);

					if (empty($found)) continue;

					$files = array_merge($files, $found);
				}

				self::$cache[$key] = $files;

				parent::__construct($files);
			}

			else {
				parent::__construct(self::$cache[$key]);
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
