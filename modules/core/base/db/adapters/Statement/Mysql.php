<?php
/**
 * NextCMS
 * 
 * Based on the Mysql adapter created by Jenei Viktor Attila
 * @link		http://nextcms.org
 * @category	modules
 * @package		core
 * @subpackage	base
 * @since		1.0
 * @version		2011-11-20
 */

defined('APP_VALID_REQUEST') || die('You cannot access the script directly.');

class Core_Base_Db_Adapters_Statement_Mysql extends Zend_Db_Statement
{
	protected $_preparedSql = '';
	protected $_sqlresult = null;
	protected $_columnCount = 0;
	protected $_rowCount = 0;
	
	/**
	 * @param string $sql
	 * @return void
	 * @throws Zend_Db_Statement_Exception
	 */
	public function _prepare($sql) 
	{
		$this->_preparedSql = $sql;
	}

	/**
	 * Binds a parameter to the specified variable name.
	 *
	 * @param mixed $parameter Name the parameter, either integer or string.
	 * @param mixed $variable  Reference to PHP variable containing the value.
	 * @param mixed $type      OPTIONAL Datatype of SQL parameter.
	 * @param mixed $length    OPTIONAL Length of SQL parameter.
	 * @param mixed $options   OPTIONAL Other options.
	 * @return bool
	 * @throws Zend_Db_Statement_Exception
	 */
	protected function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null) 
	{
		return true;
	}

	/**
	 * Closes the cursor and the statement.
	 * @return bool
	 */
	public function close() 
	{
		if (strlen($this->_preparedSql) > 0) {
			$this->_preparedSql = '';
		}
		if ($this->_sqlresult && is_resource($this->_sqlresult)) {
			@mysql_free_result($this->_sqlresult);
		}
	
		return false;
	}

	/**
	 * Closes the cursor, allowing the statement to be executed again.
	 * @return bool
	 */
	public function closeCursor() 
	{
		if ($this->_sqlresult && is_resource($this->_sqlresult)) {
			return @mysql_free_result($this->_sqlresult);
		}
		return false;
	}

	/**
	 * Returns the number of columns in the result set.
	 * Returns null if the statement has no result set metadata.
	 * @return int The number of columns.
	 */
	public function columnCount() 
	{
		return $this->_columnCount;
	}

	/**
	 * Retrieves the error code, if any, associated with the last operation on
	 * the statement handle.
	 * @return string error code.
	 */
	public function errorCode() 
	{
		if (strlen($this->_preparedSql) > 0 || $this->_sqlresult) {
			return mysql_errno($this->_adapter->getConnection());
		}
		return false;
	}

	/**
	 * Retrieves an array of error information, if any, associated with the
	 * last operation on the statement handle.
	 * @return array
	 */
	public function errorInfo() 
	{
		if (!$this->_preparedSql) {
			return false;
		}
		return array(
			mysql_errno($this->_adapter->getConnection()),
			mysql_error($this->_adapter->getConnection())
		);
	}

	/**
	 * Executes a prepared statement.
	 * @param array $params OPTIONAL Values to bind to parameter placeholders.
	 * @return bool
	 * @throws Zend_Db_Statement_Exception
	 */
	public function _execute(array $params = null) 
	{
		if (!$this->_preparedSql) {
			return false;
		}

		if ($params === null) {
			$params = $this->_bindParam;
		}

		$retval = true;
		$query = $this->_preparedSql;

		if ($params) {
			foreach ($params as &$v) {
				$v = ( $v === null ) ? 'NULL' : "'" . mysql_real_escape_string( $v, $this->_adapter->getConnection() ) . "'";
			}
			$query = vsprintf(str_replace("?", "%s", $query), $params);
		} else {
			$query = $query;
		}
		$this->_sqlresult = @mysql_query($query, $this->_adapter->getConnection());
		if (!$this->_sqlresult) {
			$retval = false;
			require_once 'Zend/Db/Statement/Exception.php';
			throw new Zend_Db_Statement_Exception('Mysql statement execute error : ' . mysql_error( $this->_adapter->getConnection() ),
				mysql_errno( $this->_adapter->getConnection() )
			);
		}
		$this->_columnCount = is_resource($this->_sqlresult) ? @mysql_num_fields($this->_sqlresult) : 0;
		$this->_rowCount = @mysql_affected_rows($this->_adapter->getConnection());
		return $retval;
	}

	/**
	 * Fetches a row from the result set.
	 * @param int $style  OPTIONAL Fetch mode for this fetch operation.
	 * @param int $cursor OPTIONAL Absolute, relative, or other.
	 * @param int $offset OPTIONAL Number for absolute or relative cursors.
	 * @return mixed Array, object, or scalar depending on fetch mode.
	 * @throws Zend_Db_Statement_Exception
	 */
	public function fetch($style = null, $cursor = null, $offset = null) 
	{
		if (!$this->_sqlresult) {
			return false;
		}

		if ($style === null) {
			$style = $this->_fetchMode;
		}

		$row = null;
		switch ($style) {
			case Zend_Db::FETCH_NUM:
				$row = mysql_fetch_row($this->_sqlresult);
				break;
			case Zend_Db::FETCH_ASSOC:
				$row = mysql_fetch_assoc($this->_sqlresult);
				break;
			case Zend_Db::FETCH_BOTH:
				$row = mysql_fetch_assoc($this->_sqlresult);
				if ($row !== false) {
					$row = array_merge($this->_sqlresult, array_values($row));
				}
				break;
			case Zend_Db::FETCH_OBJ:
				$row = mysql_fetch_object($this->_sqlresult);
				break;
			case Zend_Db::FETCH_BOUND:
				$row = mysql_fetch_assoc($this->_sqlresult);
				if ($row !== false) {
					$row = array_merge($row, array_values($row));
					$row = $this->_fetchBound($row);
				}
				break;
			default:
				break;
		}
		return $row;
	}

	/**
	 * Retrieves the next rowset (result set) for a SQL statement that has
	 * multiple result sets.  An example is a stored procedure that returns
	 * the results of multiple queries.
	 * @return bool
	 * @throws Zend_Db_Statement_Exception
	 */
	public function nextRowset()
	{
		/**
		 * @see Zend_Db_Statement_Exception
		 */
		require_once 'Zend/Db/Statement/Exception.php';
		throw new Zend_Db_Statement_Exception( __FUNCTION__.'() is not implemented' );
	}

	/**
	 * Returns the number of rows affected by the execution of the
	 * last INSERT, DELETE, or UPDATE statement executed by this
	 * statement object.
	 * @return int The number of rows affected.
	 */
	public function rowCount()
	{
		if (!$this->_sqlresult) {
			return false;
		}
		return $this->_rowCount;
	}
}
