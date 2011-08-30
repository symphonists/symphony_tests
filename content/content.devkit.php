<?php

	require_once TOOLKIT . '/class.devkit.php';
	require_once EXTENSIONS . '/symphony_tests/lib/class.symphonytest.php';

	class Content_TestsDevKit extends DevKit {
		protected $extensions;
		protected $handle;
		protected $parent;
		protected $target;
		protected $tests;

		public function __construct(){
			parent::__construct();

			$this->_title = __('Tests');
			$this->_query_string = parent::__buildQueryString(array('symphony-page', 'tests'));

			if (!empty($this->_query_string)) {
				$this->_query_string = '&amp;' . General::sanitize($this->_query_string);
			}
		}

		public function build() {
			$this->target = (strlen(trim($_GET['tests'])) == 0 ? null : $_GET['tests']);
			list($this->parent, $this->handle) = explode('.', $this->target);

			$this->extensions['workspace'] = __('Workspace');
			$this->extensions['symphony'] = __('Symphony');

			foreach (new SymphonyTestIterator() as $filename) {
				$test = SymphonyTest::load($filename);
				$info = SymphonyTest::readInformation($test);
				$info->instance = $test;
				$info->handle = $test->handle;

				if ($info->{'in-symphony'} == true) {
					$info->parent = 'symphony';
				}

				else if ($info->{'in-workspace'} == true) {
					$info->parent = 'workspace';
				}

				else if ($info->{'in-extension'} == true) {
					$info->parent = $info->extension;

					if (isset($this->extensions[$info->extension]) === false) {
						$extension = Symphony::ExtensionManager()
							->create($info->extension)
							->about();
						$this->extensions[$info->extension] = $extension['name'];
					}
				}

				$this->tests[$info->parent . '.' . $info->handle] = $info;
			}

			return parent::build();
		}

		protected function buildJump($wrapper) {
			$list = new XMLElement('ul');
			$extensions = array();

			$list->appendChild($this->buildJumpItem(
				__('All Tests'),
				'?tests' . $this->_query_string,
				($this->target == null)
			));

			foreach ($this->tests as $target => $info) {
				if (isset($extensions[$info->parent]) === false) {
					$extension_item = $this->buildJumpItem(
						$this->extensions[$info->parent],
						'?tests=' . $info->parent . $this->_query_string,
						($this->target == $info->parent)
					);
					$extensions[$info->parent] = new XMLElement('ul');
					$extension_item->appendChild($extensions[$info->parent]);
					$list->appendChild($extension_item);
				}

				if ($this->parent == $info->parent) {
					$extensions[$info->parent]->appendChild($this->buildJumpItem(
						$info->name,
						'?tests=' . $target . $this->_query_string,
						($this->target == $target)
					));
				}
			}

			$wrapper->appendChild($list);
		}

		public function buildContent($wrapper) {
			$this->addStylesheetToHead(URL . '/extensions/symphony_tests/assets/devkit.css', 'screen');

			if ($this->target === null) {
				$wrapper->appendChild(new XMLElement('h2', __('All Tests')));

				foreach ($this->extensions as $handle => $name) {
					$this->buildContentList($wrapper, $handle, 'h3');
				}
			}

			// Sub index page:
			else if (
				$this->target == 'workspace'
				|| $this->target == 'symphony'
				|| isset($this->extensions[$this->target])
			) {
				$this->buildContentList($wrapper, $this->target);
			}

			// View a test:
			else if (isset($this->tests[$this->target])) {
				$test = $this->tests[$this->target];
				$reporter = new SymphonyTestReporter();

				$test->instance->run($reporter);

				$wrapper->appendChild(new XMLElement('h2', $test->name));

				$fieldset = new XMLElement('fieldset');
				$fieldset->setAttribute('class', 'settings');
				$fieldset->appendChild(new XMLElement('legend', __('Description')));

				$description = new XMLElement('p');
				$description->setValue($test->description);
				$fieldset->appendChild($description);

				$wrapper->appendChild($fieldset);
				$wrapper->appendChild($reporter->getFieldset());
			}
		}

		public function buildContentList($wrapper, $parent, $heading = 'h2') {
			$list = new XMLElement('dl');
			$found = false;

			foreach ($this->tests as $target => $test) {
				if ($test->parent != $parent) continue;

				$title = new XMLElement('dt');
				$title->appendChild(Widget::Anchor(
					$test->name,
					'?tests=' . $target . $this->_query_string
				));
				$list->appendChild($title);
				$list->appendChild(new XMLElement('dd', $test->description));

				$found = true;
			}

			if ($found === true) {
				if (isset($this->extensions[$parent])) {
					$wrapper->appendChild(new XMLElement($heading, $this->extensions[$parent]));
				}

				$wrapper->appendChild($list);
			}
		}
	}

?>