<?php

/**
 * Model for handler types.
 */
class ThemeHouse_Objects_Model_HandlerType extends XenForo_Model
{
	/**
	 * Gets handler types that match the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions
	 * 
	 * @return array [handler type id] => info.
	 */
	public function getHandlerTypes(array $conditions = array(), array $fetchOptions = array())
	{
		$whereClause = $this->prepareHandlerTypeConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareHandlerTypeFetchOptions($fetchOptions);
		
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
		
		return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT handler_type.*
					' . $joinOptions['selectFields'] . '
				FROM xf_handler_type AS handler_type
				' . $joinOptions['joinTables'] . '
				WHERE ' . $whereClause . '
			', $limitOptions['limit'], $limitOptions['offset']
		), 'handler_type_id');
	} /* END ThemeHouse_Objects_Model_HandlerType::getHandlerTypes */

	/**
	 * Gets the handler type that matches the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions Options that affect what is fetched.
	 * 
	 * @return array|false
	 */
	public function getHandlerType(array $conditions = array(), array $fetchOptions = array())
	{
		$handlerTypes = $this->getHandlerTypes($conditions, $fetchOptions);
		
		return reset($handlerTypes);
	} /* END ThemeHouse_Objects_Model_HandlerType::getHandlerType */

	/**
	 * Gets a handler type by ID.
	 * 
	 * @param integer $handlerTypeId
	 * @param array $fetchOptions Options that affect what is fetched.
	 * 
	 * @return array|false
	 */
	public function getHandlerTypeById($handlerTypeId, array $fetchOptions = array())
	{
		$conditions = array('handler_type_id' => $handlerTypeId);
		
		return $this->getHandlerType($conditions, $fetchOptions);
	} /* END ThemeHouse_Objects_Model_HandlerType::getHandlerTypeById */

	/**
	 * Gets the total number of a handler type that match the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * 
	 * @return integer
	 */
	public function countHandlerTypes(array $conditions = array())
	{
		$fetchOptions = array();
		
		$whereClause = $this->prepareHandlerTypeConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareHandlerTypeFetchOptions($fetchOptions);
		
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
		
		return $this->_getDb()->fetchOne('
			SELECT COUNT(*)
			FROM xf_handler_type AS handler_type
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause . '
		');
	} /* END ThemeHouse_Objects_Model_HandlerType::countHandlerTypes */

	/**
	 * Gets all handler types titles.
	 * 
	 * @return array [handler type id] => title.
	 */
	public static function getHandlerTypeTitles()
	{
		$handlerTypes = XenForo_Model::create(__CLASS__)->getHandlerTypes();
		$titles = array();
		foreach ($handlerTypes as $handlerTypeId => $handlerType)
		{
			$titles[$handlerTypeId] = $handlerType['title'];
		}
		return $titles;
	} /* END ThemeHouse_Objects_Model_HandlerType::getHandlerTypeTitles */

	/**
	 * Gets the default handler type record.
	 * 
	 * @return array
	 */
	public function getDefaultHandlerType()
	{
		return array(
			'handler_type_id' => 0, 
			'title' => '', 
			'subtitle' => '', 
		);
	} /* END ThemeHouse_Objects_Model_HandlerType::getDefaultHandlerType */

	/**
	 * Prepares a set of conditions to select handler types against.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 * 
	 * @return string Criteria as SQL for where clause
	 */
	public function prepareHandlerTypeConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();
		
		if (isset($conditions['handler_type_ids']) && !empty($conditions['handler_type_ids']))
		{
			$sqlConditions[] = 'handler_type.handler_type_id IN (' . $db->quote($conditions['handler_type_ids']) . ')';
		}
		
		if (isset($conditions['handler_type_id']) && $conditions['handler_type_id'])
		{
			$sqlConditions[] = 'handler_type.handler_type_id = ' . $db->quote($conditions['handler_type_id']);
		}
		
		$this->_prepareHandlerTypeConditions($conditions, $fetchOptions, $sqlConditions);
		
		return $this->getConditionsForClause($sqlConditions);
	} /* END ThemeHouse_Objects_Model_HandlerType::prepareHandlerTypeConditions */

	/**
	 * Method designed to be overridden by child classes to add to set of conditions.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 * @param array $sqlConditions List of conditions as SQL snippets. May be edited if criteria requires.
	 */
	protected function _prepareHandlerTypeConditions(array $conditions, array &$fetchOptions, array &$sqlConditions)
	{
	} /* END ThemeHouse_Objects_Model_HandlerType::_prepareHandlerTypeConditions */

	/**
	 * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
	 * and returns SQL snippets to join the specified tables if required.
	 * 
	 * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
	 * 
	 * @return string containing selectFields, joinTables, orderClause keys.
	 *  		Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
	 */
	public function prepareHandlerTypeFetchOptions(array &$fetchOptions)
	{
		$selectFields = '';
		$joinTables = '';
		$orderBy = '';
		
		$this->_prepareHandlerTypeFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);
		
		return array(
			'selectFields' => $selectFields,
			'joinTables'   => $joinTables,
			'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
		);
	} /* END ThemeHouse_Objects_Model_HandlerType::prepareHandlerTypeFetchOptions */

	/**
	 * Method designed to be overridden by child classes to add to SQL snippets.
	 * 
	 * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
	 * @param string $selectFields = ', user.*, foo.title'
	 * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
	 * @param string $orderBy = 'x.y ASC, x.z DESC'
	 */
	protected function _prepareHandlerTypeFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables, &$orderBy)
	{
	} /* END ThemeHouse_Objects_Model_HandlerType::_prepareHandlerTypeFetchOptions */
}