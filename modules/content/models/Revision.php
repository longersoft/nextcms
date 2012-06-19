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
 * @version		2012-01-12
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

/**
 * Represents article revision
 *
 */
class Content_Models_Revision extends Core_Base_Models_Entity
{
	/**
	 * Revision's properties
	 * 
	 * @var array
	 */
	protected $_properties = array(
		'revision_id'	   => null,
		'comment'		   => null,
		'is_active'		   => 0,
		'versioning_date'  => null,
		'article_id'	   => null,
		'category_id'	   => 0,
		'categories'	   => null,
		'tags'			   => null,
		'type'			   => 'article',
		'title'			   => null,
		'sub_title'		   => null,
		'slug'			   => null,
		'description'	   => null,
		'meta_description' => null,
		'meta_keyword'	   => null,
		'content'		   => null,
		'layout'		   => null,
		'author'		   => null,
		'credit'		   => null,
		'featured'		   => 0,
		'image_icon'	   => 0,
		'video_icon'	   => 0,
		'icons'			   => null,
		'num_views'		   => 0,
		'image_square'	   => null,
		'image_thumbnail'  => null,
		'image_small'	   => null,
		'image_crop'	   => null,
		'image_medium'	   => null,
		'image_large'	   => null,
		'image_original'   => null,
		'cover_title'	   => null,
		'created_user'	   => null,
		'created_date'	   => null,
		'updated_user'	   => null,
		'updated_date'	   => null,
		'activated_user'   => null,
		'activated_date'   => null,
		'publishing_date'  => null,
		'status'		   => 'not_activated',
		'language'		   => null,
		'translations'	   => null,
	);
	
	/**
	 * Sets the article instance
	 * 
	 * @param Content_Models_Article $article The article instance
	 * @return Content_Models_Revision
	 */
	public function setArticle($article)
	{
		foreach ($article->getProperties() as $key => $value) {
			$this->_properties[$key] = $value;
		}
		return $this;
	}
}
