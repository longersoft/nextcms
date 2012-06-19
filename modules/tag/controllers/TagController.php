<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		tag
 * @subpackage	controllers
 * @since		1.0
 * @version		2012-05-10
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Tag_TagController extends Zend_Controller_Action
{
	/**
	 * Inits controller
	 * 
	 * @see Zend_Controller_Action::init()
	 * @return void
	 */
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('list', 'html')
					->initContext();
	}
	
	////////// BACKEND ACTIONS //////////
	
	/**
	 * Adds new tag
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
				$tag = new Tag_Models_Tag(array(
					'language' => $request->getPost('language'),
					'title'	   => $request->getPost('title'),
					'slug'	   => $request->getPost('slug'),
				));
				$tagId = Tag_Services_Tag::add($tag);
				$this->_helper->json(array(
					'result' => is_string($tagId) ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
					'tag_id' => $tagId,
					'title'  => $tag->title,
					'slug'   => $tag->slug,
				));
				break;
			default:
				$this->view->assign(array(
					'language'	=> $request->getParam('language', Core_Services_Config::get('core', 'localization_default_language', 'en_US')),
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
					'title'		=> $request->getParam('title', ''),
				));
				break;
		}
	}
	
	/**
	 * Deletes a tag
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$tagId	 = $request->getParam('tag_id');
		$tag	 = Tag_Services_Tag::getById($tagId);
		
		switch ($format) {
			case 'json':
				$result = Tag_Services_Tag::delete($tag);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign('tag', $tag);
				break;
		}
	}
	
	/**
	 * Edits a tag
	 * 
	 * @return void
	 */
	public function editAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$format	 = $request->getParam('format');
		$tagId	 = $request->getParam('tag_id');
		$tag	 = Tag_Services_Tag::getById($tagId);
		
		switch ($format) {
			case 'json':
				$tag->language = $request->getPost('language');
				$tag->title	   = $request->getPost('title');
				$tag->slug	   = $request->getPost('slug');
				$result		   = Tag_Services_Tag::update($tag);
				$this->_helper->json(array(
					'result' => $result ? 'APP_RESULT_OK' : 'APP_RESULT_ERROR',
				));
				break;
			default:
				$this->view->assign(array(
					'tagId'		=> $tagId,
					'tag'		=> $tag,
					'languages' => Zend_Json::decode(Core_Services_Config::get('core', 'localization_languages', '{"en_US":"English"}')),
				));
				break;
		}
	}
	
	/**
	 * Lists tags
	 * 
	 * @return void
	 */
	public function listAction()
	{
		Core_Services_Db::connect('master');
		
		$request = $this->getRequest();
		$q		 = $request->getParam('q');
		$format	 = $request->getParam('format');
		$default = array(
			'page'	   => 1,
			'keyword'  => null,
			'per_page' => 200,
			'language' => Core_Services_Config::get('core', 'localization_default_language', 'en_US'),
		);
		
		$criteria = $q ? Zend_Json::decode(base64_decode(rawurldecode($q))) : array();
		$criteria = array_merge($default, $criteria);
		$offset	  = ($criteria['page'] > 0) ? ($criteria['page'] - 1) * $criteria['per_page'] : 0;
		$tags	  = Tag_Services_Tag::find($criteria, $offset, $criteria['per_page']);
		$total	  = Tag_Services_Tag::count($criteria);
		
		$paginator = new Zend_Paginator(new Core_Base_Paginator_Adapter($tags, $total));
		$paginator->setCurrentPageNumber($criteria['page'])
				  ->setItemCountPerPage($criteria['per_page']);
		
		$this->view->assign(array(
			'tags'		=> $tags,
			'total'		=> $total,
			'criteria'	=> $criteria,
			'paginator' => $paginator,
		));
	}
	
	/**
	 * Suggests tags
	 * 
	 * @return void
	 */
	public function suggestAction()
	{
		Core_Services_Db::connect('slave');
		
		$tags = array();
		if (Core_Services_Config::get('tag', 'suggestion_enabled', 'false') == 'true') {
			$request	 = $this->getRequest();
			$content	 = $request->getParam('text');
			$suggestions = Tag_Services_Suggestion::suggest($content);
			if ($suggestions) {
				foreach ($suggestions as $title => $text) {
					$result = Tag_Services_Tag::find(array(
						'slug' => Core_Base_String::clean($text),
					), 0, 1);
					
					if ($result && count($result) > 0) {
						$tags[] = $result[0]->getProperties();
					} else {
						$tags[] = array(
							'title' => $text,
						);
					}
				}
			}
		}
		
		$this->_helper->json($tags);
	}
	
	/**
	 * Validates a tag
	 * 
	 * @return void
	 */
	public function validateAction()
	{
		Core_Services_Db::connect('slave');
		
		$request  = $this->getRequest();
		$language = $request->getParam('language');
		$slug	  = $request->getParam('slug');
		
		$result   = false;
		if ($language && $slug) {
			$criteria = array(
				'language' => $language,
				'slug'	   => $slug,
			);
			$tags	= Tag_Services_Tag::find($criteria, 0, 1);
			$result	= ($tags == null || count($tags) == 0) ? true : $tags[0]->tag_id;
		}
		$this->_helper->json(array(
			'result' => $result,
		));
	}
}
