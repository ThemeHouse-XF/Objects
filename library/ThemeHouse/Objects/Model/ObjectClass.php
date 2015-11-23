<?php

/**
 * Model for classes.
 */
class ThemeHouse_Objects_Model_ObjectClass extends XenForo_Model
{
	/**
	 * Gets classes that match the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions
	 * 
	 * @return array [class id] => info.
	 */
	public function getObjectClasses(array $conditions = array(), array $fetchOptions = array())
	{
		$whereClause = $this->prepareObjectClassConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareObjectClassFetchOptions($fetchOptions);
		
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
		
		return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT object_class.*
					' . $joinOptions['selectFields'] . '
				FROM xf_object_class AS object_class
				' . $joinOptions['joinTables'] . '
				WHERE ' . $whereClause . '
			', $limitOptions['limit'], $limitOptions['offset']
		), 'object_class_id');
	} /* END ThemeHouse_Objects_Model_ObjectClass::getObjectClasses */

	/**
	 * Gets the class that matches the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions Options that affect what is fetched.
	 * 
	 * @return array|false
	 */
	public function getObjectClass(array $conditions = array(), array $fetchOptions = array())
	{
		$objectClasses = $this->getObjectClasses($conditions, $fetchOptions);
		
		return reset($objectClasses);
	} /* END ThemeHouse_Objects_Model_ObjectClass::getObjectClass */

	/**
	 * Gets a class by ID.
	 * 
	 * @param integer $objectClassId
	 * @param array $fetchOptions Options that affect what is fetched.
	 * 
	 * @return array|false
	 */
	public function getObjectClassById($objectClassId, array $fetchOptions = array())
	{
		$conditions = array('object_class_id' => $objectClassId);
		
		return $this->getObjectClass($conditions, $fetchOptions);
	} /* END ThemeHouse_Objects_Model_ObjectClass::getObjectClassById */

	/**
	 * Gets the total number of a class that match the specified criteria.
	 * 
	 * @param array $conditions List of conditions.
	 * 
	 * @return integer
	 */
	public function countObjectClasses(array $conditions = array())
	{
		$fetchOptions = array();
		
		$whereClause = $this->prepareObjectClassConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareObjectClassFetchOptions($fetchOptions);
		
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
		
		return $this->_getDb()->fetchOne('
			SELECT COUNT(*)
			FROM xf_object_class AS object_class
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause . '
		');
	} /* END ThemeHouse_Objects_Model_ObjectClass::countObjectClasses */

	/**
	 * Gets all classes titles.
	 * 
	 * @return array [class id] => title.
	 */
	public static function getObjectClassTitles()
	{
		$objectClasses = XenForo_Model::create(__CLASS__)->getObjectClasses();
		$titles = array();
		foreach ($objectClasses as $objectClassId => $objectClass)
		{
			$titles[$objectClassId] = $objectClass['title'];
		}
		return $titles;
	} /* END ThemeHouse_Objects_Model_ObjectClass::getObjectClassTitles */

	/**
	 * Gets the default class record.
	 * 
	 * @return array
	 */
	public function getDefaultObjectClass()
	{
		return array(
			'object_class_id' => 0, 
			'title' => '', 
			'subtitle' => '', 
		);
	} /* END ThemeHouse_Objects_Model_ObjectClass::getDefaultObjectClass */

	/**
	 * Prepares a set of conditions to select classes against.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 * 
	 * @return string Criteria as SQL for where clause
	 */
	public function prepareObjectClassConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();
		
		if (isset($conditions['object_class_ids']) && !empty($conditions['object_class_ids']))
		{
			$sqlConditions[] = 'object_class.object_class_id IN (' . $db->quote($conditions['object_class_ids']) . ')';
		}
		
		if (isset($conditions['object_class_id']) && $conditions['object_class_id'])
		{
			$sqlConditions[] = 'object_class.object_class_id = ' . $db->quote($conditions['object_class_id']);
		}
		
		$this->_prepareObjectClassConditions($conditions, $fetchOptions, $sqlConditions);
		
		return $this->getConditionsForClause($sqlConditions);
	} /* END ThemeHouse_Objects_Model_ObjectClass::prepareObjectClassConditions */

	/**
	 * Method designed to be overridden by child classes to add to set of conditions.
	 * 
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 * @param array $sqlConditions List of conditions as SQL snippets. May be edited if criteria requires.
	 */
	protected function _prepareObjectClassConditions(array $conditions, array &$fetchOptions, array &$sqlConditions)
	{
	} /* END ThemeHouse_Objects_Model_ObjectClass::_prepareObjectClassConditions */

	/**
	 * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
	 * and returns SQL snippets to join the specified tables if required.
	 * 
	 * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
	 * 
	 * @return string containing selectFields, joinTables, orderClause keys.
	 *  		Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
	 */
	public function prepareObjectClassFetchOptions(array &$fetchOptions)
	{
		$selectFields = '';
		$joinTables = '';
		$orderBy = '';
		
		$this->_prepareObjectClassFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);
		
		return array(
			'selectFields' => $selectFields,
			'joinTables'   => $joinTables,
			'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
		);
	} /* END ThemeHouse_Objects_Model_ObjectClass::prepareObjectClassFetchOptions */

	/**
	 * Method designed to be overridden by child classes to add to SQL snippets.
	 * 
	 * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
	 * @param string $selectFields = ', user.*, foo.title'
	 * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
	 * @param string $orderBy = 'x.y ASC, x.z DESC'
	 */
	protected function _prepareObjectClassFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables, &$orderBy)
	{
	} /* END ThemeHouse_Objects_Model_ObjectClass::_prepareObjectClassFetchOptions */

	/**
	 * Determines if the given class can be edited.
	 * 
	 * @param array $objectClass
	 * @param string $errorPhraseKey By ref. More specific error, if available.
	 * @param array|null $viewingUser Viewing user reference
	 * 
	 * @return boolean
	 */
	public function canEditObjectClass(array $objectClass, &$errorPhraseKey = '', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);
		return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'editObjectClass'));
	} /* END ThemeHouse_Objects_Model_ObjectClass::canEditObjectClass */

	/**
	 * Determines if the given class can be deleted.
	 * 
	 * @param array $objectClass
	 * @param string $errorPhraseKey By ref. More specific error, if available.
	 * @param array|null $viewingUser Viewing user reference
	 * 
	 * @return boolean
	 */
	public function canDeleteObjectClass(array $objectClass, &$errorPhraseKey = '', array $viewingUser = null)
	{
		$this->standardizeViewingUserReference($viewingUser);
		return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'deleteObjectClass'));
	} /* END ThemeHouse_Objects_Model_ObjectClass::canDeleteObjectClass */
}