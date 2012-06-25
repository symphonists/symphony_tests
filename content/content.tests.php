<?php

	/**
	 * @package content
	 */

	require_once EXTENSIONS . '/symphony_tests/lib/class.symphonytest.php';

	/**
	 * Display a table view of available test cases.
	 */
	class ContentExtensionSymphony_TestsTests extends SymphonyTestPage {
		/**
		 * Create the page form.
		 */
		public function view() {
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
					array(__('Name'), 'col'),
					array(__('Description'), 'col'),
					array(__('Extension'), 'col')
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

			else foreach ($tests as $path) {
				$test = SymphonyTest::load($path);
				$info = SymphonyTest::readInformation($test);
				$row = new XMLElement('tr');

				$row->appendChild(Widget::TableData(
					Widget::Anchor(
						$info->name,
						sprintf(
							'%s/test/%s/',
							$this->root_url,
							$test->handle
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

				if ($info->{'in-symphony'}) {
					$row->appendChild(Widget::TableData(
						__('Symphony'), 'inactive'
					));
				}

				else if ($info->{'in-extension'}) {
					$extension = ExtensionManager::about($info->{'extension'});

					$row->appendChild(Widget::TableData(
						$extension['name']
					));
				}

				else if ($info->{'in-workspace'}) {
					$row->appendChild(Widget::TableData(
						__('Workspace'), 'inactive'
					));
				}

				$table->appendChild($row);
			}

			$this->Form->appendChild($table);
		}
	}
