<?php

	/**
	 * @package content
	 */

	require_once EXTENSIONS . '/symphony_tests/lib/class.symphonytest.php';

	/**
	 * Display the results of a test case.
	 */
	class ContentExtensionSymphony_TestsTest extends SymphonyTestPage {
		/**
		 * The test case being displayed.
		 */
		protected $test;

		/**
		 * Loads the appropriate test case while we have easy access to $context
		 * @param array $context The current page context/parameters.
		 */
		public function build($context) {
			if (isset($context[0]) && SymphonyTest::exists($context[0])) {
				$this->test = SymphonyTest::load($context[0]);
			}

			return parent::build($context);
		}

		/**
		 * Create the page form.
		 */
		public function view() {
			$test = $this->test;
			$info = SymphonyTest::readInformation($test);
			$reporter = new SymphonyTestReporter();

			$test->run($reporter);

			// Use 'Untitled' as page title when filter name is empty:
			$title = (
				isset($info->name) && trim($info->name) != ''
					? $info->name
					: __('Untitled')
			);

			$this->setPageType('form');
			$this->setTitle(__(
				(
					isset($test->handle)
						? '%1$s &ndash; %2$s &ndash; %3$s'
						: '%1$s &ndash; %2$s'
				),
				array(
					__('Symphony'),
					__('Tests'),
					$title
				)
			));
			$this->appendSubheading($title);
			$this->addStylesheetToHead(URL . '/extensions/symphony_tests/assets/test.css');

			$this->insertBreadcrumbs(array(
				Widget::Anchor(__('Tests'), $this->root_url. '/tests/')
			));

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Description')));

			$description = new XMLElement('p');
			$description->setValue($info->description);
			$fieldset->appendChild($description);

			$this->Form->appendChild($fieldset);
			$this->Form->appendChild($reporter->getFieldset());
		}
	}
