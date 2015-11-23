<?php

class ThemeHouse_Objects_Model_Object extends XenForo_Model
{
	/**
	 * Gets an object by ID.
	 *
	 * @param string $objectId
	 *
	 * @return array|false
	 */
	public function getObjectById($objectId)
	{
		return $this->_getDb()->fetchRow('
				SELECT *
				FROM xf_object
				WHERE object_id = ?
				', $objectId);
	}
	
	public function getObjectTitlesByClassId($classId)
	{
		return $this->_getDb()->fetchPairs('
				SELECT object_id, title
				FROM xf_object
				WHERE class_id = ?
				', $classId);
	}
	
	/**
	 * Gets objects that match the specified criteria.
	 *
	 * @param array $conditions
	 * @param array $fetchOptions
	 *
	 * @return array [object id] => info
	 */
	public function getObjects(array $conditions = array(), array $fetchOptions = array())
	{
		$whereClause = $this->prepareObjectConditions($conditions, $fetchOptions);
		$joinOptions = $this->prepareObjectFetchOptions($fetchOptions);

		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
		
		return $this->fetchAllKeyed($this->limitQueryResults('
				SELECT object.*
				' . $joinOptions['selectFields'] . '
				FROM xf_object AS object
				' . $joinOptions['joinTables'] . '
				WHERE ' . $whereClause . '
			', $limitOptions['limit'], $limitOptions['offset']
			), 'object_id');
	}
	
	public function __call($name, $arguments)
	{
		preg_match('#get([A-Z][A-Za-z]*)(ById)\b#', $name, $matches);
		if (isset($matches[1]))
		{
			if ($matches[2] == "ById")
			{
				$classId = self::convertClassNameToClassId($matches[1]);				
				array_unshift($arguments, $classId);
				return call_user_func_array(array($this, 'getById'), $arguments);
			}
			else
			{
				$classIdPlural = self::convertClassNameToClassId($matches[1]);
				/* @var $classModel ThemeHouse_Objects_Model_Class */
				$classModel = XenForo_Model::create('ThemeHouse_Objects_Model_Class');
					
				$classes = $classModel->getAllClasses();
					
				foreach ($classes as $class)
				{
					if ($classIdPlural == $class['class_id_plural'])
					{
						array_unshift($arguments, $class['class_id']);
						return call_user_func_array(array($this, 'get'), $arguments);
					}
				}
				
			}
		}
		preg_match('#_?prepare([A-Z][A-Za-z]*)(Conditions|FetchOptions)\b#', $name, $matches);
		if (isset($matches[1]))
		{
			$classId = self::convertClassNameToClassId($matches[1]);
			array_unshift($arguments, $classId);
			if ($matches[0] == "prepare" . $matches[1] . $matches[2])
			{
				return call_user_func_array(array($this, 'prepare' . $matches[2]), $arguments);
			}
			return;
		}
		throw new Exception('Call to undefined method ThemeHouse_Objects_Model_Object::'.$name.'()');
	}
	
	public static function __callstatic($name, $arguments)
	{
		preg_match('#get([A-Z][A-Za-z]*)Titles\b#', $name, $matches);
		if (isset($matches[1]))
		{
			$classId = self::convertClassNameToClassId($matches[1]);				
			array_unshift($arguments, $classId);
			
			return call_user_func_array(array('ThemeHouse_Objects_Model_Object', 'getTitles'), $arguments);
		}
		throw new Exception('Call to undefined method ThemeHouse_Objects_Model_Object::'.$name.'()');
	}
	
	public static function getTitles($classId)
	{
		$objects = XenForo_Model::create(__CLASS__)->get($classId);
		$titles = array();
		foreach ($objects as $objectId => $object)
		{
			$titles[$objectId] = $object['title'];
		}
		return $titles;
	}
	
	public static function convertClassNameToClassId($className)
	{
		$count = strlen($className);
			
		for($i=0; $i < $count; $i++)
		{
			$char = $className{$i};
			preg_match("#[A-Z]#", $char, $charMatches);
			if (!empty($charMatches))
			{
				if ($i>1)
				{
					$i++;
					$count++;
					$className = substr_replace($className, '_'.$char, $i-1, 1);
				}
			}
		}
		$classId = strtolower($className);
		return $classId;
	}
	
	/**
	 * Gets objects that match the specified criteria.
	 *
	 * @param string $classId
	 * @param array $conditions
	 * @param array $fetchOptions
	 *
	 * @return array [object id] => info
	 */
	public function get($classId, array $conditions = array(), array $fetchOptions = array())
	{
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $classId)));
		
		$whereClause = call_user_func_array(array($this, 'prepare' . $className . 'Conditions'), array($conditions, &$fetchOptions));
		$joinOptions = call_user_func_array(array($this, 'prepare' . $className . 'FetchOptions'), array(&$fetchOptions));
	
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
	
		return $this->prepareObjects($this->fetchAllKeyed($this->limitQueryResults('
			SELECT ' . $classId . '.*
			' . $joinOptions['selectFields'] . '
			FROM xf_object AS ' . $classId . '
			' . $joinOptions['joinTables'] . '
			WHERE ' . $classId . '.class_id = ? AND ' . $whereClause . '
			', $limitOptions['limit'], $limitOptions['offset']
		), 'object_id', $classId));
	}

	/**
	 * Gets objects that match the specified criteria.
	 *
	 * @param string $classId
	 * @param string $objectId
	 * @param array $conditions
	 * @param array $fetchOptions
	 *
	 * @return array [object id] => info
	 */
	public function getById($classId, $objectId, array $conditions = array(), array $fetchOptions = array())
	{
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $classId)));
	
		$whereClause = call_user_func_array(array($this, 'prepare' . $className . 'Conditions'), array($conditions, &$fetchOptions));
		$joinOptions = call_user_func_array(array($this, 'prepare' . $className . 'FetchOptions'), array(&$fetchOptions));
	
		$limitOptions = $this->prepareLimitFetchOptions($fetchOptions);
	
		return $this->prepareObject($this->_getDb()->fetchRow($this->limitQueryResults('
				SELECT ' . $classId . '.*
				' . $joinOptions['selectFields'] . '
				FROM xf_object AS ' . $classId . '
				' . $joinOptions['joinTables'] . '
				WHERE object_id = ? AND ' . $whereClause . '
				', $limitOptions['limit'], $limitOptions['offset']
		), $objectId));
	}
	
	/**
	 * Prepares a set of conditions to select objects against.
	 *
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 *
	 * @return string Criteria as SQL for where clause
	 */
	public function prepareConditions($classId, array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();
				
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $classId)));
		call_user_func_array(array($this, '_prepare' . $className . 'Conditions'), array($conditions, &$fetchOptions, &$sqlConditions));
		
		return $this->getConditionsForClause($sqlConditions);
	}

	/**
	 * Prepares a set of conditions to select objects against.
	 *
	 * @param array $conditions List of conditions.
	 * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
	 *
	 * @return string Criteria as SQL for where clause
	 */
	public function prepareObjectConditions(array $conditions, array &$fetchOptions)
	{
		$db = $this->_getDb();
		$sqlConditions = array();

		if (isset($conditions['class_ids']))
		{
			$sqlConditions[] = 'object.class_id IN (' . $db->quote($conditions['class_ids']) . ')';
		}
		
		if (isset($conditions['class_id']))
		{
			$sqlConditions[] = 'object.class_id = ' . $db->quote($conditions['class_id']);
		}
		
		if (isset($conditions['object_ids']))
		{
			$sqlConditions[] = 'object.object_id IN (' . $db->quote($conditions['object_ids']) . ')';
		}
		
		if (isset($conditions['object_id']))
		{
			$sqlConditions[] = 'object.object_id = ' . $db->quote($conditions['object_id']);
		}
		
		return $this->getConditionsForClause($sqlConditions);
	}
	
	/**
	 * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
	 * and returns SQL snippets to join the specified tables if required
	 *
	 * @param array $fetchOptions containing a 'join' integer key build from this class's FETCH_x bitfields
	 *
	 * @return array Containing selectFields, joinTables, orderClause keys.
	 * 		Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = ORDER BY x.y
	 */
	public function prepareFetchOptions($classId, array &$fetchOptions)
	{
		$selectFields = '';
		$joinTables = '';
		$orderBy = '';
	
		$className = str_replace(" ", "", ucwords(str_replace("_", " ", $classId)));
		call_user_func_array(array($this, '_prepare' . $className . 'FetchOptions'), array(&$fetchOptions, &$selectFields, &$joinTables));
	
		return array(
			'selectFields' => $selectFields,
			'joinTables'   => $joinTables,
			'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
		);
	}
	
	/**
	 * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
	 * and returns SQL snippets to join the specified tables if required
	 *
	 * @param array $fetchOptions containing a 'join' integer key build from this class's FETCH_x bitfields
	 *
	 * @return array Containing selectFields, joinTables, orderClause keys.
	 * 		Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = ORDER BY x.y
	 */
	public function prepareObjectFetchOptions(array $fetchOptions)
	{
		$selectFields = '';
		$joinTables = '';
		$orderBy = '';
		
		return array(
			'selectFields' => $selectFields,
			'joinTables'   => $joinTables,
			'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
		);
	}
	
	public function rebuildFieldChoices(XenForo_DataWriter $dw)
	{
		$classId = $dw->get('class_id');
		$objects = $this->getObjects(array('class_id' => $classId));
		$fieldChoices = array();
		foreach ($objects as $object)
		{
			$fieldChoices[$object['object_id']] = $object['title'];
		}
		$dw->setFieldChoices($fieldChoices);
	}
	
	public function prepareObject($object)
	{
		return $object;
	}
	
	public function prepareObjects(array $objects)
	{
		foreach ($objects as &$object)
		{
			$object = $this->prepareObject($object);
		}
		return $objects;
	}
}