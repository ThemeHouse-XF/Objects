<?php

/**
 * Data writer for classes.
 */
class ThemeHouse_Objects_DataWriter_ObjectClass extends XenForo_DataWriter
{
	/**
	 * Gets the fields that are defined for the table. See parent for explanation.
	 * 
	 * @return array
	 */
	protected function _getFields()
	{
		return array(
			'xf_object_class' => array(
				'object_class_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true), 
				'title' => array('type' => self::TYPE_STRING, 'required' => true), 
				'addon_id' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
				'class_id' => array('type' => self::TYPE_STRING, 'required' => true), 
			), 
		);
	} /* END ThemeHouse_Objects_DataWriter_ObjectClass::_getFields */

	/**
	 * Gets the actual existing data out of data that was passed in. See parent for explanation.
	 * 
	 * @param mixed
	 * 
	 * @return array|false
	 */
	protected function _getExistingData($data)
	{
		if (!$objectClassId = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}
		
		$objectClass = $this->_getObjectClassModel()->getObjectClassById($objectClassId);
		
		return $this->getTablesDataFromArray($objectClass);
	} /* END ThemeHouse_Objects_DataWriter_ObjectClass::_getExistingData */

	/**
	 * Gets SQL condition to update the existing record.
	 * 
	 * @return string
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'object_class_id = ' . $this->_db->quote($this->getExisting('object_class_id'));
	} /* END ThemeHouse_Objects_DataWriter_ObjectClass::_getUpdateCondition */

	/**
	 * Gets the data writer's default options.
	 * 
	 * @return array
	 */
	protected function _getDefaultOptions()
	{
	} /* END ThemeHouse_Objects_DataWriter_ObjectClass::_getDefaultOptions */

	/**
	 * Get the classes model.
	 * 
	 * @return ThemeHouse_Objects_Model_ObjectClass
	 */
	protected function _getObjectClassModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_ObjectClass');
	} /* END ThemeHouse_Objects_DataWriter_ObjectClass::_getObjectClassModel */
}