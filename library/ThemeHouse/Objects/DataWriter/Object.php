<?php
/**
* Data writer for objects.
*/
class ThemeHouse_Objects_DataWriter_Object extends XenForo_DataWriter
{
	protected function _getFields()
	{
		return array('xf_object' => array(
			'object_id'	=> array('type' => self::TYPE_UINT, 'autoIncrement' => true),
			'class_id' 	=> array('type' => self::TYPE_STRING, 'required' => true),
			'title' 	=> array('type' => self::TYPE_STRING, 'required' => true),
			'subtitle' 	=> array('type' => self::TYPE_STRING, 'default' => ''),
		));
	}

	/**
	* Gets the actual existing data out of data that was passed in. See parent for explanation.
	*
	* @param mixed
	*
	* @return array|false
	*/
	protected function _getExistingData($data)
	{
		if (!$objectId = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}

		$object = $this->_getObjectModel()->getObjectById($objectId);
		if (!$object)
		{
			return false;
		}

		return $this->getTablesDataFromArray($object);
	}
	
	/**
	 * Gets SQL condition to update the existing record.
	 *
	 * @return string
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'object_id = ' . $this->_db->quote($this->getExisting('object_id'));
	}
	
	/*
	protected function _postSave()
	{
		if (($this->isInsert() || $this->isChanged('title') || $this->isChanged('object_id')))
		{
			if (file_exists(XenForo_Autoloader::getInstance()->autoloaderClassToFile('ThemeHouse_CustomFields_Model_ObjectField')))
			{
				$objectFieldModel = XenForo_Model::create('ThemeHouse_CustomFields_Model_ObjectField');
				$objectFields = $objectFieldModel->getObjectFields(array('field_class_id' => $this->get('class_id')));
				foreach ($objectFields as $field)
				{
					$dw = XenForo_DataWriter::create('ThemeHouse_CustomFields_DataWriter_ObjectField');
					$dw->setExistingData($field);
					$this->_getObjectModel()->rebuildFieldChoices($dw);
					$dw->save();
				}
			}

			if (file_exists(XenForo_Autoloader::getInstance()->autoloaderClassToFile('ThemeHouse_CustomFields_Model_UserField')))
			{
				$userFieldModel = XenForo_Model::create('XenForo_Model_UserField');
				$userFields = $userFieldModel->getUserFields(array('class_id' => $this->get('class_id')));
				foreach ($userFields as $field)
				{
					$dw = XenForo_DataWriter::create('XenForo_DataWriter_UserField');
					$dw->setExistingData($field);
					$this->_getObjectModel()->rebuildFieldChoices($dw);
					$dw->save();
				}
			}
				
			if (file_exists(XenForo_Autoloader::getInstance()->autoloaderClassToFile('ThemeHouse_CustomFields_Model_ThreadField')))
			{
				$threadFieldModel = XenForo_Model::create('ThemeHouse_CustomFields_Model_ThreadField');
				$threadFields = $threadFieldModel->getThreadFields(array('class_id' => $this->get('class_id')));
				foreach ($threadFields as $field)
				{
					$dw = XenForo_DataWriter::create('ThemeHouse_CustomFields_DataWriter_ThreadField');
					$dw->setExistingData($field);
					$this->_getObjectModel()->rebuildFieldChoices($dw);
					$dw->save();
				}
			}
			
			if (file_exists(XenForo_Autoloader::getInstance()->autoloaderClassToFile('ThemeHouse_CustomFields_Model_PostField')))
			{
				$postFieldModel = XenForo_Model::create('ThemeHouse_CustomFields_Model_PostField');
				$postFields = $postFieldModel->getPostFields(array('class_id' => $this->get('class_id')));
				foreach ($postFields as $field)
				{
					$dw = XenForo_DataWriter::create('ThemeHouse_CustomFields_DataWriter_PostField');
					$dw->setExistingData($field);
					$this->_getObjectModel()->rebuildFieldChoices($dw);
					$dw->save();
				}
			}
		}
		parent::_postSave();
	}
	*/
		
	/**
	 * @return ThemeHouse_Objects_Model_Object
	 */
	protected function _getObjectModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_Object');
	}	
}