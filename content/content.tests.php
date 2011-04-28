<?php

	require_once EXTENSIONS . '/unit_tests/lib/class.iterator.php';
	require_once EXTENSIONS . '/unit_tests/lib/class.page.php';
	require_once EXTENSIONS . '/unit_tests/lib/class.unit-test.php';
	
	class ContentExtensionUnit_TestsTests extends UnitTestsPage {
		public function action() {
			$items = (
				(isset($_POST['items']) && is_array($_POST['items']))
					? array_keys($_POST['items'])
					: null
			);
			
			// Delete selected emails:
			if ($_POST['with-selected'] == 'delete' && !empty($items)) {
				//EmailBuilderEmail::deleteAll($items);
			}
		}
		
		public function view() {
			$filters = new UnitTestsIterator();
			
			$this->setPageType('table');
			$this->setTitle(__(
				'%1$s &ndash; %2$s',
				array(
					__('Symphony'),
					__('Unit Tests')
				)
			));
			
			$this->appendSubheading(__('Unit Tests'));
			
			$table = new XMLElement('table');
			$table->appendChild(
				Widget::TableHead(array(
					array(__('Name'), 'col'),
					array(__('Description'), 'col'),
					array(__('Extension'), 'col')
				))
			);
			$table->setAttribute('class', 'selectable');
			
			if (!$filters->valid()) {
				$table->appendChild(Widget::TableRow(array(
					Widget::TableData(
						__('None Found.'),
						'inactive',
						null, 3
					)
				)));
			}
			
			else foreach ($filters as $path) {
				$test = UnitTest::load($path);
				$info = UnitTest::readInformation($test);
				$row = new XMLElement('tr');
				
				$first_cell = Widget::TableData(
					Widget::Anchor(
						$info->name,
						sprintf(
							'%s/test/%s/',
							$this->root_url,
							$test->handle
						)
					)
				);
				$first_cell->appendChild(Widget::Input(
					sprintf('items[%d]', $test->handle),
					null, 'checkbox'
				));
				$row->appendChild($first_cell);
				
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
				
				if ($info->{'in-extension'}) {
					$extension = (object)Symphony::ExtensionManager()->create($info->{'extension'})->about();
					
					$row->appendChild(Widget::TableData(
						$extension->name
					));
				}
				
				else if ($info->{'in-symphony'}) {
					$row->appendChild(Widget::TableData(
						__('Symphony'), 'inactive'
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
			
			/*
			$actions = new XMLElement('div');
			$actions->setAttribute('class', 'actions');
			
			$actions->appendChild(Widget::Input('action[apply]', __('Run Tests'), 'submit'));
			
			$this->Form->appendChild($actions);
			*/
		}
	}
	
?>