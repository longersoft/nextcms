<?php
/**
 * NextCMS
 * 
 * @author		Nguyen Huu Phuoc <thenextcms@gmail.com>
 * @copyright	Copyright (c) 2011 - 2012, Nguyen Huu Phuoc
 * @license		http://nextcms.org/license.txt	(GNU GPL version 2 or later)
 * @link		http://nextcms.org
 * @category	modules
 * @package		content
 * @subpackage	services
 * @since		1.0
 * @version		2012-05-23
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Content_Services_Article
{
	/**
	 * Adds new article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return string Id of newly created article
	 */
	public static function add($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->add($article);
	}
	
	/**
	 * Gets the number of articles by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public static function count($criteria = array())
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->count($criteria);
	}
	
	/**
	 * Counts the number of articles in a given range of date
	 * 
	 * @param array $criteria May include:
	 * - category_id
	 * - status
	 * - language
	 * - type: Type of article
	 * @param string $lowerDate The lower date of range in format of Y-m-d H:i:s.
	 * If it is not defined, it will be set as the first day of current month
	 * @param string $upperDate The upper date of range in format of Y-m-d H:i:s
	 * If it is not defined, it will be set as the last day of current month
	 * @return array An array which maps the date and number of articles in that date
	 */
	public static function countByDate($criteria = array(), $lowerDate = null, $upperDate = null)
	{
		if ($lowerDate == null) {
			$lowerDate = date('Y-m') . '-01';
		}
		if ($upperDate == null) {
			$upperDate = date('Y-m') . '-31';
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->countByDate($criteria, $lowerDate, $upperDate);
	}
	
	/**
	 * Deletes given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return bool
	 */
	public static function delete($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn		= Core_Services_Db::getConnection();
		$articleDao = Core_Services_Dao::factory(array(
											'module' => 'content',
											'name'   => 'Article',
									   ))
									   ->setDbConnection($conn);
		if ($article->status == Content_Models_Article::STATUS_DELETED) {
			$articleDao->delete($article);
		} else {
			$article->status = Content_Models_Article::STATUS_DELETED;
			$articleDao->updateStatus($article);
		}
		return true;
	}
	
	/**
	 * Empties the trash
	 * 
	 * @return bool
	 */
	public static function emptyTrash()
	{
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->emptyTrash();
		return true;
	}
	
	/**
	 * Finds articles by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public static function find($criteria = array(), $offset = null, $count = null)
	{
		if (isset($criteria['keyword']) && $criteria['keyword']) {
			$criteria['keyword'] = Core_Base_Hook_Registry::getInstance()->executeFilter('Core_SanitizeInput', $criteria['keyword']);
		}
		
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->find($criteria, $offset, $count);
	}
	
	/**
	 * Gets the articles in a given range of date
	 * 
	 * @param array $criteria Consists of the following keys:
	 * - category_id
	 * - status
	 * - type
	 * @param string $lowerDate The lower date of range in format of Y-m-d H:i:s.
	 * If it is not defined, it will be set as the first day of current month
	 * @param string $upperDate The upper date of range in format of Y-m-d H:i:s
	 * If it is not defined, it will be set as the last day of current month
	 * @return array An array that maps the date with all articles activated in that date
	 */
	public static function findByDate($criteria, $lowerDate = null, $upperDate = null)
	{
		if ($lowerDate == null) {
			$lowerDate = date('Y-m') . '-01';
		}
		if ($upperDate == null) {
			$upperDate = date('Y-m') . '-31';
		}
		$conn	= Core_Services_Db::getConnection();
		$result = Core_Services_Dao::factory(array(
										'module' => 'content',
										'name'   => 'Article',
								   ))
								   ->setDbConnection($conn)
								   ->findByDate($criteria, $lowerDate, $upperDate);
		$return = array();
		if ($result) {
			foreach ($result as $row) {
				$date = date('Y-m-d', strtotime($row->activated_date));
				if (!isset($return[$date])) {
					$return[$date] = array();
				}
				$return[$date][] = $row;
			}
		}
		return $return;
	}
	
	/**
	 * Gets article by given Id
	 * 
	 * @param string $articleId Id of the article
	 * @return Content_Models_Article|null
	 */
	public static function getById($articleId)
	{
		if (!$articleId) {
			throw new Exception("The article's Id is required");
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->getById($articleId);
	}
	
	/**
	 * Increases the number of views of article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return bool
	 */
	public static function increaseNumViews($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumViews($article);
		return true;
	}
	
	/**
	 * Moves given article to other category
	 * 
	 * @param string $articleId Article's Id
	 * @param string $categoryId Category's Id
	 * @return bool
	 */
	public static function move($articleId, $categoryId)
	{
		if (!$articleId || !$categoryId) {
			throw new Exception("The article's Id and category's Id are required");
		}
		$article = self::getById($articleId);
		if (!$article) {
			throw new Core_Base_Exception_NotFound('Cannot find the article with Id of ' . $articleId); 
		}
		
		// Define the array of categories
		$categories = array($categoryId);
		if ($article->categories) {
			$categories = explode(',', $article->categories);
			if ($article->category_id) {
				$categories = array_diff($categories, array($article->category_id));
			}
			$categories = array_merge($categories, array($categoryId));
		}
		
		$article->category_id	   = $categoryId;
		$article->categories	   = $categories;
		$article->new_translations = null;
		
		return self::update($article);
	}
	
	/**
	 * Updates given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return bool
	 */
	public static function update($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->update($article);
		return true;
	}
	
	/**
	 * Updates article's cover
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @param array $thumbnails
	 * @return bool
	 */
	public static function updateCover($article, $thumbnails)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The first param is not an instance of Content_Models_Article');
		}
		if (!is_array($thumbnails)) {
			return false;
		}
		
		foreach (array('square', 'thumbnail', 'small', 'crop', 'medium', 'large', 'original') as $size) {
			if (isset($thumbnails[$size])) {
				$thumb = 'image_' . $size;
				$article->$thumb = $thumbnails[$size];
			}
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->updateCover($article);
		return true;
	}
	
	/**
	 * Updates the order of article
	 * 
	 * @param string $articleId Id of article
	 * @param string $categoryId Id of category that article belongs to
	 * @param int $index The index of article
	 * @return bool
	 */
	public static function updateOrder($articleId, $categoryId, $index)
	{
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->updateOrder($articleId, $categoryId, $index);
		return true;
	}
	
	/**
	 * Gets the previous activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public static function getPrevArticle($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->getPrevArticle($article);
	}
	
	/**
	 * Gets the next activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public static function getNextArticle($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->getNextArticle($article);
	}
	
	/**
	 * Updates article's status
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return bool
	 */
	public static function updateStatus($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		if (!$article->status || !in_array($article->status, Content_Models_Article::$STATUS)) {
			throw new Exception('Invalid status');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->updateStatus($article);
		// Execute hooks
		if ($article->status == Content_Models_Article::STATUS_ACTIVATED) {
			$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
			$url  = $view->serverUrl() . $view->url($article->getProperties(), 'content_article_view');
			Core_Base_Hook_Registry::getInstance()->executeAction('Content_Activate_Article', array($article, $url));
		}
		
		return true;
	}
	
	/**
	 * Updates the username field. It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public static function updateUsername($user)
	{
		if (!$user || !($user instanceof Core_Models_User)) {
			throw new Exception('The param is not an instance of Core_Models_User');
		}
		$conn = Core_Services_Db::connect('master');
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->updateUsername($user);
	}
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between articles and tags after removing a tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public static function deleteTag($tag)
	{
		if (!$tag || !($tag instanceof Tag_Models_Tag)) {
			throw new Exception('The param is not an instance of Tag_Models_Tag');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->deleteTag($tag);
	}
	
	/**
	 * Gets tags of given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return array
	 */
	public static function getTags($article)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		return Core_Services_Dao::factory(array(
									'module' => 'content',
									'name'   => 'Article',
								))
								->setDbConnection($conn)
								->getTags($article);
	}
	
	/**
	 * Sets tags to given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return bool
	 */
	public static function setTags($article, $tags)
	{
		if (!$article || !($article instanceof Content_Models_Article)) {
			throw new Exception('The param is not an instance of Content_Models_Article');
		}
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->setTags($article, $tags);
		return true;
	}
	
	////////// MANAGE COMMENTS //////////
	
	/**
	 * Called after updating status of a comment
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @return void
	 */
	public static function updateCommentStatus($comment)
	{
		if ($comment->entity_class != 'Content_Models_Article') {
			return;
		}
		$inc = ($comment->status == Comment_Models_Comment::STATUS_ACTIVATED) ? '+1' : '-1';
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->increaseNumComments($comment, $inc);
	}
	
	////////// MANAGE ARTICLES IN FOLDERS //////////	
	
	/**
	 * Adds an article to a folder
	 * 
	 * @param string $articleId The Id of article
	 * @param string $folderId The Id of folder
	 * @return bool
	 */
	public static function addToFolder($articleId, $folderId)
	{
		if ($articleId == null || empty($articleId) || $folderId == null || empty($folderId)) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->addToFolder($articleId, $folderId);
		return true;
	}
	
	/**
	 * Removes an article from a folder
	 * 
	 * @param string $articleId The Id of article
	 * @param string $folderId The Id of folder
	 * @return bool
	 */
	public static function removeFromFolder($articleId, $folderId)
	{
		if ($articleId == null || empty($articleId) || $folderId == null || empty($folderId)) {
			return false;
		}
		
		$conn = Core_Services_Db::getConnection();
		Core_Services_Dao::factory(array(
							'module' => 'content',
							'name'   => 'Article',
						 ))
						 ->setDbConnection($conn)
						 ->removeFromFolder($articleId, $folderId);
		return true;
	}
}
