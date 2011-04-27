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
			$this->setTitle(__('Symphony &ndash; Unit Tests'));
			
			$this->appendSubheading(__('Unit Tests'));
			
			$table = new XMLElement('table');
			$table->appendChild(
				Widget::TableHead(array(
					array(__('Name'), 'col'),
					array(__('Author'), 'col'),
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
				$filter = UnitTest::load($path);
				$row = new XMLElement('tr');
				
				$first_cell = Widget::TableData(
					Widget::Anchor(
						$filter->about()->name,
						sprintf(
							'%s/test/%s/',
							$this->root_url,
							$filter->handle
						)
					)
				);
				$first_cell->appendChild(Widget::Input(
					sprintf('items[%d]', $filter->handle),
					null, 'checkbox'
				));
				$row->appendChild($first_cell);

				if (isset($filter->about()->author->website)) {
					$row->appendChild(Widget::TableData(Widget::Anchor(
						$filter->about()->author->name,
						General::validateURL($filter->about()->author->website)
					)));
				}
				
				else if (isset($filter->about()->author->email)) {
					$row->appendChild(Widget::TableData(Widget::Anchor(
						$filter->about()->author->name,
						'mailto:' . $filter->about()->author->email
					)));	
				}
				
				else {
					$row->appendChild(Widget::TableData(
						$filter->about()->author->name
					));
				}
				
				if ($filter->extension instanceof Extension) {
					$extension = (object)$filter->extension->about();
					
					$row->appendChild(Widget::TableData(
						$extension->name
					));
				}
				
				else if (strpos($filter->getFileName(), SYMPHONY . '/') === 0) {
					$row->appendChild(Widget::TableData(
						__('Symphony'), 'inactive'
					));
				}
				
				else if (strpos($filter->getFileName(), WORKSPACE . '/') === 0) {
					$row->appendChild(Widget::TableData(
						__('Workspace'), 'inactive'
					));
				}
				
				else {
					$row->appendChild(Widget::TableData(
						__('Other'), 'inactive'
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