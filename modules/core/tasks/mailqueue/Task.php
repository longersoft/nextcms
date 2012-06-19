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
 * @subpackage	tasks
 * @since		1.0
 * @version		2012-03-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Tasks_Mailqueue_Task extends Core_Base_Extension_Task
{
	/**
	 * The number of mails could be sent each time running the task
	 * 
	 * @var int
	 */
	const DEFAULT_MAX_MAILS	  = 50;
	
	/**
	 * The maximum number of sending attempts
	 * 
	 * @var int
	 */
	const DEFAULT_MAX_ATTEMPTS = 1;
	
	/**
	 * @see Core_Base_Extension_Task::execute()
	 */
	public function execute($params = null)
	{
		Core_Services_Db::connect('master');

		// Get the options
		$options  = Core_Services_Task::getOptionsByInstance($this);
		
		$maxMails = self::DEFAULT_MAX_MAILS;
		if ($options && isset($options['max_mails'])) {
			$maxMails = (int) $options['max_mails'];
		}
		
		$maxAttempts = self::DEFAULT_MAX_ATTEMPTS;
		if ($options && isset($options['max_attempts'])) {
			$maxAttempts = (int) $options['max_attempts'];
		}
		
		// Get the list of mails
		$mails = Core_Services_MailQueue::find(array(
			'success'	   => 0,
			'max_attempts' => $maxAttempts,
		), 0, $maxMails);
		
		// Send mails
		// http://framework.zend.com/manual/en/zend.mail.multiple-emails.html
		if ($mails == null || count($mails) == 0) {
			return;
		}
		
		$transport = Core_Services_Mail::getMailTransport();
		foreach ($mails as $mailQueue) {
			$success = true;
			try {
				$mail = new Zend_Mail();
				$mail->setFrom($mailQueue->from_email, $mailQueue->from_name)					
					 ->addTo($mailQueue->to_email, $mailQueue->to_name)
					 ->setSubject($mailQueue->subject)
					 ->setBodyHtml($mailQueue->content)
					 ->send($transport);
			} catch (Exception $ex) {
				$success = false;
			}
			
			$now = date('Y-m-d H:i:s');
			$mailQueue->last_attempt = $now;
			$mailQueue->sent_date	 = $success ? $now : null;
			$mailQueue->success		 = $success ? 1 : 0;
			
			if ($success) {
				Core_Services_MailQueue::dequeue($mailQueue);
			} else {
				if ((int) $mailQueue->num_attempts + 1 >= $maxAttempts) {
					Core_Services_MailQueue::dequeue($mailQueue);
				} else {
					// Increase the number of attempts
					Core_Services_MailQueue::increaseAttempts($mailQueue);
				}
			}
		}
	}
	
	/**
	 * Shows the configuration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$options = Core_Services_Task::getOptionsByInstance($this);
		$this->view->assign(array(
			'maxMails'	  => $options ? $options['max_mails'] : self::DEFAULT_MAX_MAILS,
			'maxAttempts' => $options ? $options['max_attempts'] : self::DEFAULT_MAX_ATTEMPTS,
		));
	}
	
	/**
	 * Saves the settings
	 * 
	 * @return string
	 */
	public function saveAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$options = array(
			'max_mails'	   => $request->getParam('max_mails', self::DEFAULT_MAX_MAILS),
			'max_attempts' => $request->getParam('max_attempts', self::DEFAULT_MAX_ATTEMPTS),
		);
		$result = Core_Services_Task::setOptionsForInstance($this, $options);
		return $result ? 'true' : 'false';
	}
}
