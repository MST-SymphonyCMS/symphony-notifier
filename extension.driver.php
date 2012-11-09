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
					'delegate' => 'CustomActions',
					'callback' => 'savePreferences'
				),

				// Register for CRUD events
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
				)
			);
		}
		
		// Controller method
		public function notify($context){
			include_once(TOOLKIT . '/class.gateway.php');
     	$ch = new Gateway;
      
      $ch->init();
      $ch->setopt('URL', 'http://rpc.notifier.com/');
      $ch->setopt('POST', 1);
      $ch->setopt('CONTENTTYPE', 'text/xml');
      
      $xml = new XMLElement('methodCall');
      $xml->appendChild(new XMLElement('methodName', 'weblogUpdates.ping'));
      
      $params = new XMLElement('params');
      
      $param = new XMLElement('param');       
      $param->appendChild(new XMLElement('value', Symphony::Configuration()->get('sitename', 'general')));
      $params->appendChild($param);            

      $param = new XMLElement('param');
      $param->appendChild(new XMLElement('value', URL));
      $params->appendChild($param);    
      
      $xml->appendChild($params);        

      $ch->setopt('POSTFIELDS', $xml->generate(true, 0));

      $ch->exec(GATEWAY_FORCE_SOCKET);
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

			// notify url of notifier event
			$notify_url = Symphony::Configuration()->get('notify_url', 'notifier');
			$notify_label = Widget::Label(__('Send modification event to URL'));
			$notify_label->appendChild(Widget::Input('settings[notifier][notify_url]', $notify_url, 'text'));
			$group->appendChild($notify_label);
		}

		public function savePreferences() {
		
			// Remove existing configuration settings.
			Symphony::Configuration()->remove('notifier');
			Administration::instance()->saveConfig();
			
			// If there are Notifier settings, format them
			if(is_array($_POST['settings']['notifier'])){
				foreach($_POST['settings']['notifier'] as $preference => $value){
					if(is_array($value)){
						$_POST['settings']['notifier'][$preference] = implode(',',$value);
					}
				}
			}
		}
		
	}