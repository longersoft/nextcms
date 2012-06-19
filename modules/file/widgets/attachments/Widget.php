<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		file
 * @subpackage	widgets
 * @since		1.0
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_Widgets_Attachments_Widget extends Core_Base_Extension_Widget
{
	/**
	 * Shows the widget configuaration form
	 * 
	 * @return void
	 */
	public function configAction()
	{
		Core_Services_Db::connect('master');
		
		$request	   = $this->getRequest();
		$language	   = $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US'));
		$attachmentIds = $request->getParam('attachment_ids');
		$dataSource	   = $request->getParam('data_source');
		
		$attachments   = array();
		if ($dataSource == 'set' && $attachmentIds) {
			$resultSet = File_Services_Attachment::find(array(
				'attachment_ids' => $attachmentIds,
			));
			foreach ($resultSet as $attachment) {
				$attachments[] = $attachment->getProperties(array('attachment_id', 'title', 'slug', 'hash', 'language'));
			}
		}
		
		$this->view->assign(array(
			'language'	  => $language,
			'languages'   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
			'uid'	      => uniqid(),
			'attachments' => $attachments,
		));
	}
	
	/**
	 * Shows the attachments
	 * 
	 * @return void
	 */
	public function showAction()
	{
		Core_Services_Db::connect('slave');
		
		$request     = $this->getRequest();
		$criteria    = array(
			'language' => $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
		);
		$count       = $request->getParam('limit', 20);
		$dataSource  = $request->getParam('data_source');
		
		switch ($dataSource) {
			// Get the attachments by their Ids
			case 'set':
				$attachmentIds = $request->getParam('attachment_ids', array());
				$criteria = array(
					'attachment_ids' => $attachmentIds,
				);
				break;
			
			// Most downloaded attachments
			case 'most_downloaded':
				$criteria['sort_by']  = 'num_downloads';
				$criteria['sort_dir'] = 'DESC';
				break;
				
			// Get the latest attachments
			case 'latest':
			default:
				// Sort by the activated date
				$criteria['sort_by']  = 'uploaded_date';
				$criteria['sort_dir'] = 'DESC';
				break;
		}
		$attachments = File_Services_Attachment::find($criteria, 0, $count);
		
		$this->view->assign(array(
			'title'			 => $request->getParam('title', ''),
			'attachments'	 => $attachments,
			'numAttachments' => $attachments ? count($attachments) : 0,
		));
	}
}
