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
 * @subpackage	models
 * @since		1.0
 * @version		2012-05-03
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

interface Content_Models_Dao_Interface_Article
{
	/**
	 * Adds new article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return string Id of newly created article
	 */
	public function add($article);
	
	/**
	 * Gets the number of articles by given collection of conditions
	 * 
	 * @param array $criteria
	 * @return int
	 */
	public function count($criteria = array());
	
	/**
	 * Counts the number of articles in a given range of date
	 * 
	 * @param array $criteria May include:
	 * - category_id
	 * - status
	 * - language
	 * - type: Type of article
	 * @param string $lowerDate The lower date of range in format of YYYY-mm-dd H:i:s.
	 * @param string $upperDate The upper date of range in format of YYYY-mm-dd H:i:s
	 * @return array An array which maps the date and number of articles in that date
	 */
	public function countByDate($criteria = array(), $lowerDate, $upperDate);
	
	/**
	 * Deletes given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return void
	 */
	public function delete($article);
	
	/**
	 * Empties the trash
	 * 
	 * @return void
	 */
	public function emptyTrash();
	
	/**
	 * Finds articles by given collection of conditions
	 * 
	 * @param array $criteria
	 * @param int $offset
	 * @param int $count
	 * @return Core_Base_Models_RecordSet
	 */
	public function find($criteria = array(), $offset = null, $count = null);
	
	/**
	 * Gets the articles in a given range of date
	 * 
	 * @param array $criteria Consists of the following keys:
	 * - category_id
	 * - status
	 * - type
	 * @param string $lowerDate The lower date of range in format of Y-m-d H:i:s.
	 * @param string $upperDate The upper date of range in format of Y-m-d H:i:s
	 * @return Core_Base_Models_RecordSet
	 */
	public function findByDate($criteria, $lowerDate, $upperDate);
	
	/**
	 * Gets article by given Id
	 * 
	 * @param string $articleId Id of the article
	 * @return Content_Models_Article|null
	 */
	public function getById($articleId);
	
	/**
	 * Increases the number of views of article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return void
	 */
	public function increaseNumViews($article);
	
	/**
	 * Updates given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return void
	 */
	public function update($article);
	
	/**
	 * Updates article's cover
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return void
	 */
	public function updateCover($article);
	
	/**
	 * Updates the order of article
	 * 
	 * @param string $articleId Id of article
	 * @param string $categoryId Id of category that article belongs to
	 * @param int $index The index of article
	 * @return void
	 */
	public function updateOrder($articleId, $categoryId, $index);
	
	/**
	 * Gets the previous activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public function getPrevArticle($article);
	
	/**
	 * Gets the next activated article in the same category with given article
	 * 
	 * @param Content_Models_Article $article The article
	 * @return Content_Models_Article
	 */
	public function getNextArticle($article);
	
	/**
	 * Updates article's status
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return void
	 */
	public function updateStatus($article);
	
	/**
	 * It is called as a callback after user updates the username
	 * 
	 * @param Core_Models_User $user The user instance
	 * @return void
	 */
	public function updateUsername($user);
	
	////////// MANAGE TAGS //////////
	
	/**
	 * Deletes association between articles and tags after removing a tag
	 * 
	 * @param Tag_Models_Tag $tag The tag instance
	 * @return void
	 */
	public function deleteTag($tag);
	
	/**
	 * Gets tags of given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return array
	 */
	public function getTags($article);
	
	/**
	 * Sets tags to given article
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @param array $tags Array of tags. Each item is an instance of Tag_Models_Tag
	 * @return void
	 */
	public function setTags($article, $tags);
	
	////////// MANAGE COMMENTS //////////
	
	/**
	 * Increases or decreases the number of comments of given article
	 * 
	 * @param Comment_Models_Comment $comment The comment instance
	 * @param string $inc Can be "+1" or "-1"
	 * @return void
	 */
	public function increaseNumComments($comment, $inc);
	
	////////// MANAGE ARTICLES IN FOLDERS //////////	
	
	/**
	 * Adds an article to a folder
	 * 
	 * @param string $articleId The Id of article
	 * @param string $folderId The Id of folder
	 * @return void
	 */
	public function addToFolder($articleId, $folderId);
	
	/**
	 * Removes an article from a folder
	 * 
	 * @param string $articleId The Id of article
	 * @param string $folderId The Id of folder
	 * @return void
	 */
	public function removeFromFolder($articleId, $folderId);
}
