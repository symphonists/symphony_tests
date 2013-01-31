<?php

	/**
	 * @package content
	 */

	require_once EXTENSIONS . '/symphony_tests/lib/class.symphonytest.php';

	/**
	 * Navigate and run test cases.
	 */
	class ContentExtensionSymphony_TestsTests extends SymphonyTestPage {
		/**
		 * Create the page form.
		 */
		public function view() {
			$tests = new SymphonyTestIterator();
			$path = realpath(DOCROOT . '/' . implode('/', $this->_context));

			if (strpos($path, DOCROOT) !== 0) {
				throw new Exception('Access denied.');
			}

			if ($path === DOCROOT) {
				$this->viewIndex();
			}

			else if (is_dir($path)) {
				$this->viewLocation($path);
			}

			else if (is_file($path)) {
				$this->viewTest($path);
			}
		}

		public function viewIndex() {
			$tests = new SymphonyTestIterator();

			$this->setPageType('table');
			$this->setTitle(__(
				'%1$s &ndash; %2$s',
				array(
					__('Symphony'),
					__('Tests')
				)
			));
			$this->appendSubheading(__('Tests'));

			$table = new XMLElement('table');

			$table->appendChild(
				Widget::TableHead(array(
					array(__('Group'), 'col'),
					array(__('Tests'), 'col')
				))
			);

			if (!$tests->valid()) {
				$table->appendChild(Widget::TableRow(array(
					Widget::TableData(
						__('None Found.'),
						'inactive',
						null, 3
					)
				)));
			}

			else {
				$groups = array();

				foreach ($tests as $path) {
					$test = SymphonyTest::load($path);
					$info = SymphonyTest::readInformation($test);

					if ($info->{'in-symphony'}) {
						$dir = substr(dirname($path), strlen(DOCROOT) + 1);

						if (isset($groups[$dir]) === false) {
							$groups[$dir] = (object)array(
								'name' =>	__('Symphony'),
								'tests' =>	0
							);
						}

						$groups[$dir]->tests++;
					}

					else if ($info->{'in-extension'}) {
						$about = ExtensionManager::about($info->{'extension'});
						$dir = substr(dirname($path), strlen(DOCROOT) + 1);

						if (isset($groups[$dir]) === false) {
							$groups[$dir] = (object)array(
								'name' =>	$about['name'],
								'tests' =>	0
							);
						}

						$groups[$dir]->tests++;
					}

					else if ($info->{'in-workspace'}) {
						$dir = substr(dirname($path), strlen(DOCROOT) + 1);

						if (isset($groups[$dir]) === false) {
							$groups[$dir] = (object)array(
								'name' =>	__('Workspace'),
								'tests' =>	0
							);
						}

						$groups[$dir]->tests++;
					}
				}

				// Workspace first, Symphony last:
				uksort($groups, function($a, $b) {
					if ($a === 'workspace/tests') return -2;
					if ($b === 'workspace/tests') return 2;
					if ($a === 'extensions/symphony_tests/core-tests') return 2;
					if ($b === 'extensions/symphony_tests/core-tests') return -2;

					return strcasecmp($a, $b);
				});

				foreach ($groups as $handle => $data) {
					$row = new XMLElement('tr');

					$row->appendChild(Widget::TableData(
						Widget::Anchor(
							$data->name,
							sprintf(
								'%s/tests/%s/',
								$this->root_url,
								$handle
							)
						)
					));

					$row->appendChild(Widget::TableData(
						$data->tests
					));

					$table->appendChild($row);
				}
			}

			$this->Form->appendChild($table);
		}

		public function viewLocation($path) {
			$title = $this->getLocationTitle($path);
			$tests = new SymphonyTestIterator(array($path));

			// Prepare page:
			$this->setPageType('table');
			$this->setTitle(__(
				'%1$s &ndash; %2$s &ndash; %3$s',
				array(
					__('Symphony'),
					__('Tests'),
					$title
				)
			));
			$this->appendSubheading($title);
			$this->insertBreadcrumbs(array(
				Widget::Anchor(__('Tests'), SYMPHONY_URL . '/extension/symphony_tests/tests/'),
			));

			$table = new XMLElement('table');
			$table->appendChild(
				Widget::TableHead(array(
					array(__('Name'), 'col'),
					array(__('Description'), 'col')
				))
			);

			foreach ($tests as $path) {
				$dir = substr($path, strlen(DOCROOT) + 1);
				$test = SymphonyTest::load($path);
				$info = SymphonyTest::readInformation($test);
				$row = new XMLElement('tr');

				$row->appendChild(Widget::TableData(
					Widget::Anchor(
						$info->name,
						sprintf(
							'%s/tests/%s/',
							$this->root_url,
							$dir
						)
					)
				));

				if ($info->description) {
					$row->appendChild(Widget::TableData(
						$info->description
					));
				}

				else {
					$row->appendChild(Widget::TableData(
						__('None'), 'inactive'
					));
				}

				$table->appendChild($row);
			}

			$this->Form->appendChild($table);
		}

		public function viewTest($path) {
			$reporter = new SymphonyTestReporter();
			$test = SymphonyTest::load($path);
			$info = SymphonyTest::readInformation($test);
			$title = $info->name;
			$sub_path = dirname($path);
			$sub_title = $this->getLocationTitle($sub_path);

			// Prepare page:
			$this->setPageType('form');
			$this->setTitle(__(
				'%1$s &ndash; %2$s &ndash; %3$s &ndash; %4$s',
				array(
					__('Symphony'),
					__('Tests'),
					$sub_title,
					$title
				)
			));
			$this->appendSubheading($title);
			$this->addStylesheetToHead(URL . '/extensions/symphony_tests/assets/test.css');
			$this->insertBreadcrumbs(array(
				Widget::Anchor(
					__('Tests'),
					SYMPHONY_URL
					. '/extension/symphony_tests/tests/'
				),
				Widget::Anchor(
					$sub_title,
					SYMPHONY_URL
					. '/extension/symphony_tests/tests/'
					. substr($sub_path, strlen(DOCROOT) + 1)
					. '/'
				)
			));

			$test->run($reporter);

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Description')));

			$description = new XMLElement('p');
			$description->setValue($info->description);
			$fieldset->appendChild($description);

			$this->Form->appendChild($fieldset);
			$this->Form->appendChild($reporter->getFieldset());
		}

		public function getLocationTitle($path) {
			// Find the 'Symphony' tests directory:
			$extension = Symphony::ExtensionManager()
				->create('symphony_tests');
			$core_path = $extension->getExtensionDir();
			$core_path .= '/core-tests';

			// Viewing 'Workspace' tests:
			if (strpos($path, WORKSPACE) === 0) {
				return __('Workspace');
			}

			// Viewing 'Symphony' tests:
			else if (strpos($path, $core_path) === 0) {
				return __('Symphony');
			}

			// Viewing an extensions tests:
			else if (strpos($path, EXTENSIONS) === 0) {
				$name = basename(dirname($path));
				$about = ExtensionManager::about($name);

				return $about['name'];
			}

			return false;
		}
	}
