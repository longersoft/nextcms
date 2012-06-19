<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-21
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_ConfigController extends Zend_Controller_Action
{
	////////// BACKEND ACTIONS //////////

	/**
	 * Configures the Core module
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format  = $request->getParam('format');
		$currentLanguage = Core_Services_Config::get('core', 'language_code', 'en_US');
		$config	 = Core_Services_Config::getAppConfigs();
		
		switch ($format) {
			case 'json':
				$redirectTo = null;
				
				// General settings
				Core_Services_Config::set('core', 'site_name', $request->getPost('site_name', ''));
				Core_Services_Config::set('core', 'site_title', $request->getPost('site_title', ''));
				Core_Services_Config::set('core', 'meta_keyword', $request->getPost('meta_keyword', ''));
				Core_Services_Config::set('core', 'meta_description', $request->getPost('meta_description', ''));
				Core_Services_Config::set('core', 'charset', $request->getPost('charset', 'utf-8'));
				Core_Services_Config::set('core', 'datetime_timezone', $request->getPost('datetime_timezone'));
				Core_Services_Config::set('core', 'url_static', $request->getPost('url_static'));
				
				// Notification settings
				$notificationPosition = $request->getPost('notification_position', 'tr-down');
				$notificationDuration = $request->getPost('notification_duration', 2);
				Core_Services_Config::set('core', 'notification_position', $notificationPosition);
				Core_Services_Config::set('core', 'notification_duration', $notificationDuration);
				
				// Advanced settings
				if ($request->getPost('crontask_disabled')) {
					unset($config['resources']['frontController']['plugins']['Core_Controllers_Plugins_CronTask']);
				} else {
					$config['resources']['frontController']['plugins']['Core_Controllers_Plugins_CronTask'] = 'Core_Controllers_Plugins_CronTask';
				}
				
				// Feed settings
				Core_Services_Config::set('core', 'feed_limit', $request->getPost('feed_limit', 20));
				Core_Services_Config::set('core', 'feed_title', $request->getPost('feed_title'));
				Core_Services_Config::set('core', 'feed_description', $request->getPost('feed_description'));
				Core_Services_Config::set('core', 'feed_copyright', $request->getPost('feed_copyright'));
				Core_Services_Config::set('core', 'feed_generator', $request->getPost('feed_generator'));
				
				// Session settings
				$currentSessionHandler = Core_Services_Config::get('core', 'session_handler', 'db');
				$newSessionHandler	   = $request->getPost('session_handler', 'db');
				Core_Services_Config::set('core', 'session_handler', $newSessionHandler);
				Core_Services_Config::set('core', 'session_cookie_domain', $request->getPost('session_cookie_domain', ''));
				Core_Services_Config::set('core', 'session_cookie_lifetime', $request->getPost('session_cookie_lifetime', '3600'));
				
				$sessionMemcacheLifetime = $request->getPost('session_memcache_lifetime', 3600);
				$sessionMemcacheIdPrefix = $request->getPost('session_memcache_id_prefix', '');
				Core_Services_Config::set('core', 'session_memcache_lifetime', $sessionMemcacheLifetime);
				Core_Services_Config::set('core', 'session_memcache_id_prefix', $sessionMemcacheIdPrefix);
				
				$config['session_cache']['frontend'] = array(
					'name'	  => 'Core',
					'options' => array(
						'lifetime'				  => $sessionMemcacheLifetime,
						'automatic_serialization' => true,
						'cache_id_prefix'		  => $sessionMemcacheIdPrefix,
					),
				);
				switch ($newSessionHandler) {
					case 'file':
					case 'db':
						unset($config['session_cache']);
						break;
					case 'memcache':
						$config['session_cache']['backend'] = array(
							'name'	  => 'Memcached',
							'options' => array(
								'servers' => array(),
							),
						);
						// Get the memcache servers
						$sessionMemcacheServers = array();
						if (($servers = $request->getPost('session_memcache_servers')) && ($ports = $request->getPost('session_memcache_ports'))) {
							foreach ($servers as $index => $server) {
								if ($servers[$index] && $ports[$index]) {
									$sessionMemcacheServers['server_' . $index] = array(
										'host' => $servers[$index],
										'port' => $ports[$index],
									);
								}
							}
						}
						$config['session_cache']['backend']['options']['servers'] = $sessionMemcacheServers;
						Core_Services_Config::set('core', 'session_memcache_servers', Zend_Json::encode($sessionMemcacheServers));
						break;
				}
				
				Core_Services_Config::set('core', 'secret_key', $request->getPost('secret_key', md5(Core_Base_String::generateRandomString())));
				Core_Services_Config::set('core', 'accesslog_enabled', $request->getPost('accesslog_enabled') ? 'true' : 'false');
				
				$currentAdminPrefix = Core_Services_Config::get('core', 'admin_prefix', 'admin');
				$newAdminPrefix		= $request->getPost('admin_prefix');
				if ($newAdminPrefix && $newAdminPrefix != $currentAdminPrefix) {
					Core_Services_Config::set('core', 'admin_prefix', $newAdminPrefix);
					
					$dashboardRoutes = APP_ROOT_DIR . DS . 'modules' . DS . 'core' . DS . 'configs' . DS . 'routes' . DS . 'dashboard.php';
					$configRoutes	 = APP_ROOT_DIR . DS . 'modules' . DS . 'core' . DS . 'configs' . DS . 'routes' . DS . 'config.php';
					Core_Services_Route::loadRoutesFromFile($dashboardRoutes, $newAdminPrefix);
					Core_Services_Route::loadRoutesFromFile($configRoutes, $newAdminPrefix);
					
					$redirectTo = $this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index') . '#u=' . $this->view->url(array(), 'core_config_config');
				}
				
				// Captcha settings
				$captchaAdapter = $request->getPost('captcha_adapter', 'image');
				if ($captchaAdapter == 'recaptcha') {
					if (($recaptchaPublicKey = $request->getPost('recaptcha_public_key')) && ($recaptchaPrivateKey = $request->getPost('recaptcha_private_key'))) {
						Core_Services_Config::set('core', 'captcha_adapter', $captchaAdapter);
						Core_Services_Config::set('core', 'captcha_options', Zend_Json::encode(array(
							'recaptcha_public_key'  => $recaptchaPublicKey,
							'recaptcha_private_key' => $recaptchaPrivateKey,
						)));
					}
				} else {
					Core_Services_Config::set('core', 'captcha_adapter', $captchaAdapter);
				}
				
				// Performance settings
				$compressCss   = $request->getPost('compress_css');
				$compressJs    = $request->getPost('compress_js');
				Core_Services_Config::set('core', 'compress_css', $compressCss ? 'true' : 'false');
				Core_Services_Config::set('core', 'compress_js', $compressJs ? 'true' : 'false');
				if ($compressCss) {
					$this->view->style()->setupCaching();
				}
				if ($compressJs) {
					$this->view->script()->setupCaching();
				}
				
				$cacheSystem   = $request->getPost('cache_system', '');
				$cacheLifetime = $request->getPost('cache_lifetime', 3600);
				$cacheIdPrefix = $request->getPost('cache_id_prefix', '');
				Core_Services_Config::set('core', 'cache_system', $cacheSystem);
				Core_Services_Config::set('core', 'cache_lifetime', $cacheLifetime);
				Core_Services_Config::set('core', 'cache_id_prefix', $cacheIdPrefix);
				
				$config['cache']['frontend'] = array(
					'name'	  => 'Core',
					'options' => array(
						'lifetime'				  => $cacheLifetime,
						'automatic_serialization' => true,
						'cache_id_prefix'		  => $cacheIdPrefix,
					),
				);
				
				switch ($cacheSystem) {
					case 'file':
						$config['cache']['backend'] = array(
							'name'	  => 'File',
							'options' => array(
								'cache_dir' => '{APP_TEMP_DIR}{DS}cache',
							),
						);
						break;
					case 'memcache':
						$config['cache']['backend'] = array(
							'name'	  => 'Memcached',
							'options' => array(
								'servers' => array(),
							),
						);
						// Get the memcache servers
						$memcacheServers = array();
						if (($servers = $request->getPost('cache_servers')) && ($ports = $request->getPost('cache_ports'))) {
							foreach ($servers as $index => $server) {
								if ($servers[$index] && $ports[$index]) {
									$memcacheServers['server_' . $index] = array(
										'host' => $servers[$index],
										'port' => $ports[$index],
									);
								}
							}
						}
						$config['cache']['backend']['options']['servers'] = $memcacheServers;
						Core_Services_Config::set('core', 'memcache_servers', Zend_Json::encode($memcacheServers));
						break;
					case '':
					default:
						unset($config['cache']);
						break;
				}
				Core_Services_Config::writeAppConfigs($config);
				
				// Mail settings
				Core_Services_Config::set('core', 'mail', Zend_Json::encode(array(
					'protocol' => $request->getPost('mail_protocol', 'mail'),
					'host'	   => $request->getPost('mail_host'),
					'port'	   => $request->getPost('mail_port'),
					'username' => $request->getPost('mail_username'),
					'password' => $request->getPost('mail_password'),
					'ssl'	   => $request->getPost('mail_security'),
				)));
				
				// Mail templates
				Core_Services_Config::set('core', 'sending_password_template', Zend_Json::encode(array(
					'from_name'   => $request->getPost('sending_password_from_name'),
					'from_email'  => $request->getPost('sending_password_from_email'),
					'subject'     => $request->getPost('sending_password_subject'),
					'content'     => $request->getPost('sending_password_content'),
				)));
				
				// Update language setting
				$newLanguage = $request->getPost('language_code');
				$languageDir = $request->getPost('language_direction');
				Core_Services_Config::set('core', 'language_code', $newLanguage);
				Core_Services_Config::set('core', 'language_direction', $languageDir);
				
				// Localization setting
				Core_Services_Config::set('core', 'localization_default_language', $request->getPost('localization_default_language'));
				$localizationLanguages = array();
				if ($selectedLocalizationLanguages = $request->getPost('localization_languages')) {
					foreach ($selectedLocalizationLanguages as $item) {
						$item = $this->view->encoder()->decode($item);
						$localizationLanguages[$item['locale']] = $item['name'];
					}
				}
				Core_Services_Config::set('core', 'localization_languages', Zend_Json::encode($localizationLanguages));
				
				// Authentication setting
				Core_Services_Config::set('core', 'num_login_attempts', $request->getPost('num_login_attempts', 3));
				Core_Services_Config::set('core', 'register_enabled', $request->getPost('register_enabled') ? 'true' : 'false');
				Core_Services_Config::set('core', 'register_openid_enabled', $request->getPost('register_openid_enabled') ? 'true' : 'false');
				Core_Services_Config::set('core', 'register_auto_activate', $request->getPost('register_auto_activate') ? 'true' : 'false');
				Core_Services_Config::set('core', 'register_blocked_usernames', $request->getPost('register_blocked_usernames'));
				Core_Services_Config::set('core', 'register_email_activate', $request->getPost('register_email_activate') ? 'true' : 'false');
				Core_Services_Config::set('core', 'activating_account_template', Zend_Json::encode(array(
					'from_name'   => $request->getPost('activating_account_from_name'),
					'from_email'  => $request->getPost('activating_account_from_email'),
					'subject'     => $request->getPost('activating_account_subject'),
					'content'     => $request->getPost('activating_account_content'),
				)));
				if ($defaultRole = $request->getPost('register_default_role')) {
					Core_Services_Config::set('core', 'register_default_role', $defaultRole);			
				}
				
				$this->_helper->json(array(
					'result'				=> 'APP_RESULT_OK',
					'notification_position' => $notificationPosition,
					'notification_duration' => $notificationDuration,
					'reload'				=> ($newSessionHandler != $currentSessionHandler) || ($newLanguage != $currentLanguage) || ($languageDir != $this->view->APP_LANGUAGE_DIR),
					'redirectTo'			=> $redirectTo,
				));
				break;
			default:
				// General settings
				$charsets = array(
					'utf-8' => 'Unicode (UTF-8)',	// Default charsets
				);
				$charsetsFile = APP_ROOT_DIR . DS . 'data' . DS . 'charsets.php';
				if (file_exists($charsetsFile)) {
					$charsets = include $charsetsFile;
				}
				
				Zend_Locale::disableCache(true);
				$timeZones = Zend_Locale::getTranslationList('WindowsToTimezone', $currentLanguage);
				ksort($timeZones);
				
				// Language setting
				$languages = array();
				$file	   = APP_ROOT_DIR . DS . 'data' . DS . 'i18n.php';
				if (!file_exists($file)) {
					$languages[] = array(
						'en_US' => array(
							'english' => 'English',
							'native'  => 'English',
						),
					);
				} else {
					$languages = include $file;
				}
				
				// Localization setting
				$localizationLanguages = array();
				$file	   = APP_ROOT_DIR . DS . 'data' . DS . 'l10n.php';
				if (!file_exists($file)) {
					$localizationLanguages[] = array(
						'en_US' => array(
							'english' => 'English',
							'native'  => 'English',
						),
					);
				} else {
					$localizationLanguages = include $file;
				}
				
				// Registration settings
				$registerEnabled = Core_Services_Config::get('core', 'register_enabled', 'true') == 'true';
				
				$mailOptions = Core_Services_Config::get('core', 'mail');
				$mailOptions = $mailOptions 
								? Zend_Json::decode($mailOptions) 
								: array(
										'protocol' => 'mail',
										'host'	   => '',
										'port'	   => '',
										'username' => '',
										'password' => '',
										'ssl'	   => '',
									);
				
				$memcacheServers = Core_Services_Config::get('core', 'memcache_servers');
				$memcacheServers = $memcacheServers ? Zend_Json::decode($memcacheServers) : array();
				
				$sessionMemcacheServers = Core_Services_Config::get('core', 'session_memcache_servers');
				$sessionMemcacheServers = $sessionMemcacheServers ? Zend_Json::decode($sessionMemcacheServers) : array();
				
				// Captcha settings
				$captchaOptions = Core_Services_Config::get('core', 'captcha_options');
				$captchaOptions = $captchaOptions
								? Zend_Json::decode($captchaOptions)
								: array(
									'recaptcha_public_key'  => '',
									'recaptcha_private_key' => '',
								);
				
				$this->view->assign(array(
					'charsets'						=> $charsets,
					'timeZones'						=> $timeZones,
					'language'						=> Core_Services_Config::get('core', 'language_code', 'en_US'),
					'languages'						=> $languages,
					'localizationLanguages'			=> $localizationLanguages,
					'selectedLocalizationLanguages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'crontaskEnabled'				=> isset($config['resources']['frontController']['plugins']) && in_array('Core_Controllers_Plugins_CronTask', array_values($config['resources']['frontController']['plugins'])),
					'registerEnabled'				=> $registerEnabled,
					'roles'							=> Core_Services_Role::find(),
					'mailOptions'					=> $mailOptions,
					'sendingPasswordMailTemplate'	=> Core_Services_Mail::getBuiltinTemplate('core', 'sending_password_template'),
					'activatingAccountMailTemplate' => Core_Services_Mail::getBuiltinTemplate('core', 'activating_account_template'),
					'sessionMemcacheServers'		=> $sessionMemcacheServers,
					'memcacheServers'				=> $memcacheServers,
					'captchaAdapter'				=> Core_Services_Config::get('core', 'captcha_adapter', 'image'),
					'captchaOptions'				=> $captchaOptions,
				));
				break;
		}
	}
}
