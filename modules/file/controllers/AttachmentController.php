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
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-06-19
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class File_AttachmentController extends Zend_Controller_Action
{
	////////// FRONTEND ACTIONS //////////
	
	/**
	 * Downloads attachment
	 * 
	 * @return void
	 */
	public function downloadAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$attachmentId = $request->getParam('attachment_id');
		$hash		  = $request->getParam('hash');
		$slug		  = $request->getParam('slug');
		
		if ($attachmentId) {
			$attachment = File_Services_Attachment::getById($attachmentId);
		} else {
			$attachments = File_Services_Attachment::find(array(
															'hash' => $hash,
															'slug' => $slug,
														), 0, 1);
			$attachment  = ($attachments && count($attachments) > 0) ? $attachments[0] : null;
		}
		if ($attachment == null) {
			throw new Core_Base_Exception_NotFound('Cannot find the attachment');
		}
		
		// Check if the attachment requires authentication to download
		$authRequired = ($attachment->auth_required == 1) && !Zend_Auth::getInstance()->hasIdentity();
		
		// Check if the attachment requires password to download
		$passRequired = $attachment->password;
		
		if ($request->isPost()) {
			if ($authRequired == false && ($passRequired == null || empty($passRequired) || File_Services_Attachment::checkPassword($request->getPost('password', ''), $passRequired))) {
				// Download the attachment
				$file = APP_ROOT_DIR . DS . ltrim(str_replace('/', DS, $attachment->path), DS);
				if (file_exists($file)) {
					// Increase the number of downloads
					Core_Services_Counter::register($attachment, 'downloads', 'File_Services_Attachment::increaseNumDownloads', array($attachment));
					
					// Update the last download
					$attachment->last_download = date('Y-m-d H:i:s');
					File_Services_Attachment::updateLastDownload($attachment);
					
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename($file));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					ob_clean();
					flush();
					readfile($file);
					exit();
				} else {
					$this->_helper
						 ->getHelper('FlashMessenger')
						 ->addMessage($this->view->translator()->_('attachment.download.notFound'));
					$this->_redirect($this->view->url($attachment->getProperties(), 'file_attachment_download'));
				}
			} else {
				$this->_helper
					 ->getHelper('FlashMessenger')
					 ->addMessage($this->view->translator()->_('attachment.download.error'));
				$this->_redirect($this->view->url($attachment->getProperties(), 'file_attachment_download'));
			}
		} else {
			$request->setParam('entity_class', get_class($attachment))
					->setParam('entity_id', $attachment->attachment_id);
			
			$this->view->assign(array(
				'attachment'   => $attachment,
				'authRequired' => $authRequired,
				'passRequired' => $passRequired,
			));
		}
	}
	
	////////// BACKEND ACTIONS //////////

	/**
	 * Adds new attachment
	 * 
	 * @return void
	 */
	public function addAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		switch ($format) {
			case 'json':
				$result = array();
				if (isset($_FILES['uploadedfiles'])) {
					$attachment = new File_Models_Attachment(array(
						'title'			=> $request->getPost('title'),
						'slug'			=> $request->getPost('slug'),
						'description'	=> $request->getPost('description'),
						'uploaded_user' => Zend_Auth::getInstance()->getIdentity()->user_id,
						'uploaded_date' => date('Y-m-d H:i:s'),
						'num_downloads' => 0,
						'auth_required' => ($request->getPost('auth_required') == null) ? 0 : 1,
						'language'		=> $request->getPost('language'),
						'translations'  => $request->getPost('translations'),
					));
					$password = $request->getPost('password');
					if ($password && !empty($password)) {
						$attachment->password = $password;
					}
					
					$attachment->file = $_FILES['uploadedfiles'];
					
					// Add new attachment
					$attachmentId = File_Services_Attachment::add($attachment);
					$result[] = array(
						'path' => $attachment->path,
					);
				}
				
				$this->_helper->json($result);
				break;
			default:
				$sourceId = $request->getParam('source_id');
				$source   = $sourceId ? File_Services_Attachment::getById($sourceId) : null;
				
				$this->view->assign(array(
					'source'	=> $source,
					'language'	=> $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Deletes attachment
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$attachmentId = $request->getParam('attachment_id');
		$attachment	  = File_Services_Attachment::getById($attachmentId);
		
		switch ($format) {
			case 'json':
				$result = File_Services_Attachment::delete($attachment);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('attachment', $attachment);
				break;
		}
	}
	
	/**
	 * Edits attachment
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request	  = $this->getRequest();
		$format		  = $request->getParam('format');
		$attachmentId = $request->getParam('attachment_id');
		$attachment	  = File_Services_Attachment::getById($attachmentId);
		
		switch ($format) {
			case 'json':
				$result = array();
				if ($attachment) {
					if (isset($_FILES['uploadedfiles'])) {
						$attachment->file = $_FILES['uploadedfiles'];
					}
					
					$attachment->title		   = $request->getPost('title');
					$attachment->slug		   = $request->getPost('slug');
					$attachment->description   = $request->getPost('description');
					$attachment->auth_required = ($request->getPost('auth_required') == null) ? 0 : 1;
					$password	   			   = $request->getPost('password');
					if ($password === null) {
						$attachment->password = null;
					} else if ($password != '') {
						$attachment->password = File_Services_Attachment::encryptPassword($password);
					}
					
					// Update translation
					$attachment->new_translations = $request->getPost('translations');
					if (!$attachment->new_translations) {
						$attachment->new_translations = Zend_Json::encode(array(
							$attachment->language => (string) $attachment->attachment_id,
						));
					}
					
					File_Services_Attachment::update($attachment);
					$result[] = array(
						'path' => $attachment->path,
					);
				}
				
				$this->_helper->json($result);
				break;
			default:
				// Get the translations of the attachments
				$translations = null;
				if ($attachment) {
					$languages = Zend_Json::decode($attachment->translations);
					unset($languages[$attachment->language]);
					$translations = array();
					foreach ($languages as $locale => $id) {
						$translations[] = File_Services_Attachment::getById($id);
					}
				}
				
				$this->view->assign(array(
					'attachment'   => $attachment,
					'attachmentId' => $attachmentId,
					'languages'	   => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'translations' => $translations,
				));
				break;
		}
	}
	
	/**
	 * Lists attachments
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		
		$q		 = $request->getParam('q');
		$default = array(
			'page'	   => 1,
			'keyword'  => null,
			'per_page' => 20,
			'language' => null,
		);
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		
		switch ($format) {
			case 'json':
				$offset		 = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
				$attachments = File_Services_Attachment::find($criteria, $offset, $criteria['per_page']);
				$total		 = File_Services_Attachment::count($criteria);
				
				// Build data for the grid
				$items	 = array();
				$fields	 = array('attachment_id', 'title', 'slug', 'name', 'hash', 'extension', 'size', 'num_downloads', 'uploaded_date', 'language', 'translations');
				foreach ($attachments as $attachment) {
					$item = array();
					foreach ($fields as $field) {
						$item[$field] = $attachment->$field;
					}
					$item['password_required'] = $attachment->password ? true : false;
					$item['link'] = $this->view->url($attachment->getProperties(), 'file_attachment_download');
					$items[] = $item;
				}
				
				// Paginator
				$paginatorTopic = $request->getParam('topic', '/app/file/attachment/list/onGotoPage');
				$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($attachments, $total));
				$paginator->setCurrentPageNumber($criteria['page'])
						  ->setItemCountPerPage($criteria['per_page']);
				
				$data = array(
					// Data for the grid
					'data' => array(
						'identifier' => 'attachment_id',
						'items'		 => $items,
					),
					// Paginator
					'paginator' => $this->view->paginator('slidingToolbar')->render($paginator, "javascript: dojo.publish('" . $paginatorTopic . "', [__PAGE__]);"),
				);
				$this->_helper->json($data);
				break;
			default:
				$this->view->assign('criteria', $criteria);
				break;
		}		
	}
}
