<?php

	/**
	 * @package symphony_tests
	 */

	/**
	 * A SimpleTest interface for Symphony.
	 */
	class Extension_Symphony_Tests extends Extension {
		/**
		 * Extension information.
		 */
		public function about() {
			return array(
				'name'			=> 'Symphony Tests',
				'version'		=> '0.2',
				'release-date'	=> '2011-08-29',
				'author'		=> array(
					array(
						'name'			=> 'Rowan Lewis',
						'website'		=> 'http://rowanlewis.com/',
						'email'			=> 'me@rowanlewis.com'
					)
				)
			);
		}

		/**
		 * Cleanup installation.
		 */
		public function uninstall() {
			Symphony::Configuration()->remove('symphony_tests');
		}

		/**
		 * Create configuration.
		 */
		public function install() {
			Symphony::Configuration()->set('navigation_group', __('System'), 'symphony_tests');
			Administration::instance()->saveConfig();

			return true;
		}

		/**
		 * Listen for these delegates.
		 */
		public function getSubscribedDelegates() {
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'viewPreferences'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'actionsPreferences'
				)
			);
		}

		/**
		 * Add navigation items.
		 */
		public function fetchNavigation() {
			$group = $this->getNavigationGroup();

			return array(
				array(
					'location'	=> $group,
					'name'		=> __('Tests'),
					'link'		=> '/tests/'
				),
				array(
					'location'	=> $group,
					'name'		=> __('Tests'),
					'link'		=> '/test/',
					'visible'	=> 'no'
				)
			);
		}

		/**
		 * Get the current extension directory.
		 */
		public function getExtensionDir() {
			return dirname(__FILE__);
		}

		/**
		 * True when no navigation group has been specified.
		 */
		protected $missing_navigation_group;

		/**
		 * Get the name of the desired navigation group.
		 */
		public function getNavigationGroup() {
			if ($this->missing_navigation_group === true) return null;

			return Symphony::Configuration()->get('navigation_group', 'symphony_tests');
		}

		/**
		 * Get a list of available navigation groups.
		 */
		public function getNavigationGroups() {
			$sectionManager = new SectionManager(Symphony::Engine());
			$sections = $sectionManager->fetch(null, 'ASC', 'sortorder');
			$options = array();

			if (is_array($sections)) foreach ($sections as $section) {
				$options[] = $section->get('navigation_group');
			}

			$options[] = __('Blueprints');
			$options[] = __('System');

			return array_unique($options);
		}

		/**
		 * Validate preferences before saving.
		 * @param array $context
		 */
		public function actionsPreferences($context) {
			if (
				!isset($context['settings']['symphony_tests']['navigation_group'])
				|| trim($context['settings']['symphony_tests']['navigation_group']) == ''
			) {
				$context['errors']['symphony_tests']['navigation_group'] = __('This is a required field.');
				$this->missing_navigation_group = true;
			}
		}

		/**
		 * View preferences.
		 * @param array $context
		 */
		public function viewPreferences($context) {
			$wrapper = $context['wrapper'];
			$errors = Symphony::Engine()->Page->_errors;

			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Email Builder')));

			$label = Widget::Label(
				__('Navigation Group')
				. ' <i>'
				. __('Created if does not exist')
				. '</i>'
			);
			$label->appendChild(Widget::Input(
				'settings[symphony_tests][navigation_group]',
				$this->getNavigationGroup()
			));

			if (isset($errors['symphony_tests']['navigation_group'])) {
				$label = Widget::wrapFormElementWithError($label, $errors['symphony_tests']['navigation_group']);
			}

			$fieldset->appendChild($label);

			$list = new XMLElement('ul');
			$list->setAttribute('class', 'tags singular');

			foreach ($this->getNavigationGroups() as $group) {
				$list->appendChild(new XMLElement('li', $group));
			}

			$fieldset->appendChild($list);
			$wrapper->appendChild($fieldset);
		}
	}

?>