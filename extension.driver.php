<?php

Class Extension_Notifier extends Extension{
		
	public function getSubscribedDelegates(){
		return array(
			// Register for preferences
			array(
				'page' => '/system/preferences/',
				'delegate' => 'AddCustomPreferenceFieldsets',
				'callback' => 'appendPreferences'
			),
			array(
				'page' => '/system/preferences/',
				'delegate' => 'Save',
				'callback' => 'savePreferences'
			),

			// Register for Enrty CRUD events
			array(
				'page' => '/publish/',
				'delegate' => 'EntryPreDelete',
				'callback' => 'notify'
			),
			array(
				'page' => '/publish/new/',
				'delegate' => 'EntryPostCreate',
				'callback' => 'notify'
			),
			array(
				'page' => '/publish/edit/',
				'delegate' => 'EntryPostEdit',
				'callback' => 'notify'
			),

			// Register for Datasource CRUD events
			array(
				'page'		=> '/blueprints/datasources/',
				'delegate'	=> 'DatasourcePostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/datasources/',
				'delegate'	=> 'DatasourcePostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/datasources/',
				'delegate'	=> 'DatasourcePreDelete',
				'callback'	=> 'notify'
			),

			// Register for Page CRUD events
			array(
				'page'		=> '/blueprints/pages/',
				'delegate'	=> 'PagePostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/pages/',
				'delegate'	=> 'PagePostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/pages/',
				'delegate'	=> 'PagePreDelete',
				'callback'	=> 'notify'
			),

			// Register for Event CRUD events
			array(
				'page'		=> '/blueprints/events/',
				'delegate'	=> 'EventPostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/events/',
				'delegate'	=> 'EventPostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/events/',
				'delegate'	=> 'EventPreDelete',
				'callback'	=> 'notify'
			),

			// Register for Utility CRUD events
			array(
				'page'		=> '/blueprints/utilities/',
				'delegate'	=> 'UtilityPostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/utilities/',
				'delegate'	=> 'UtilityPostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/utilities/',
				'delegate'	=> 'UtilityPreDelete',
				'callback'	=> 'notify'
			),

			// Register for Section CRUD events
			array(
				'page'		=> '/blueprints/sections/',
				'delegate'	=> 'SectionPostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/sections/',
				'delegate'	=> 'SectionPostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/blueprints/sections/',
				'delegate'	=> 'SectionPreDelete',
				'callback'	=> 'notify'
			),

			// Register for other system events
			array(
				'page'		=> '/system/authors/',
				'delegate'	=> 'AuthorPostCreate',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/authors/',
				'delegate'	=> 'AuthorPostEdit',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/authors/',
				'delegate'	=> 'AuthorPreDelete',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/extensions/',
				'delegate'	=> 'ExtensionPreEnable',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/extensions/',
				'delegate'	=> 'ExtensionPreDisable',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/extensions/',
				'delegate'	=> 'ExtensionPreUninstall',
				'callback'	=> 'notify'
			),
			array(
				'page'		=> '/system/preferences/',
				'delegate'	=> 'Save',
				'callback'	=> 'notify'
			)
		);
	}

	
	// Controller method
	public function notify($context){
		include_once(TOOLKIT . '/class.gateway.php');
		$ch = new Gateway;
		$url = Symphony::Configuration()->get('notify_url', 'notifier');
		
		$ch->init();
		$ch->setopt('URL', $url);
		$ch->setopt('GET', 1);

		$ch->exec();
	}

	
	public function appendPreferences($context){
		include_once(TOOLKIT . '/class.authormanager.php');
		include_once(TOOLKIT . '/class.sectionmanager.php');

		// Fieldset and layout
		$group = new XMLElement('fieldset');
		$group->setAttribute('class', 'settings');
		$legend = new XMLElement('legend', __('Notifier'));
		$group->appendChild($legend);

		$div = new XMLElement('div');
		$div->setAttribute('class', 'group triple');


		// Notification URL
		$label = Widget::Label(__('Notification URL'));
		$url   = Symphony::Configuration()->get('notify_url', 'notifier');
		$input = Widget::Input('settings[notifier][notify_url]', $url, 'text');

		$label->appendChild($input);
		$div->appendChild($label);

		$group->appendChild($div);
		$context['wrapper']->appendChild($group);
	}


	public function savePreferences() {
		$settings = $_POST['settings'];

		$setting_group = 'general';
		$setting_name = 'notifier';
		$setting_value = $settings['general']['notifier'];

		Symphony::Configuration()->set($setting_name, $setting_value, $setting_group);
		Administration::instance()->saveConfig();
	}


	public function uninstall() {
		Symphony::Configuration()->remove('notifier');
		Administration::instance()->saveConfig();
	}	
}