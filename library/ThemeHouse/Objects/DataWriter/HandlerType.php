<?php

/**
 * Data writer for handler types.
 */
class ThemeHouse_Objects_DataWriter_HandlerType extends XenForo_DataWriter
{
	/**
	 * Gets the fields that are defined for the table. See parent for explanation.
	 * 
	 * @return array
	 */
	protected function _getFields()
	{
		return array(
			'xf_handler_type' => array(
				'handler_type_id' => array('type' => self::TYPE_STRING, 'required' => true), 
				'title' => array('type' => self::TYPE_STRING, 'required' => true), 
				'addon_id' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'required' => true), 
				'controller_admin_class' => array('type' => self::TYPE_STRING, 'default' => ''), 
			), 
		);
	} /* END ThemeHouse_Objects_DataWriter_HandlerType::_getFields */

	/**
	 * Gets the actual existing data out of data that was passed in. See parent for explanation.
	 * 
	 * @param mixed
	 * 
	 * @return array|false
	 */
	protected function _getExistingData($data)
	{
		if (!$handlerTypeId = $this->_getExistingPrimaryKey($data))
		{
			return false;
		}
		
		$handlerType = $this->_getHandlerTypeModel()->getHandlerTypeById($handlerTypeId);
		
		return $this->getTablesDataFromArray($handlerType);
	} /* END ThemeHouse_Objects_DataWriter_HandlerType::_getExistingData */

	/**
	 * Gets SQL condition to update the existing record.
	 * 
	 * @return string
	 */
	protected function _getUpdateCondition($tableName)
	{
		return 'handler_type_id = ' . $this->_db->quote($this->getExisting('handler_type_id'));
	} /* END ThemeHouse_Objects_DataWriter_HandlerType::_getUpdateCondition */

	/**
	 * Gets the data writer's default options.
	 * 
	 * @return array
	 */
	protected function _getDefaultOptions()
	{
	} /* END ThemeHouse_Objects_DataWriter_HandlerType::_getDefaultOptions */

	/**
	 * Get the handler types model.
	 * 
	 * @return ThemeHouse_Objects_Model_HandlerType
	 */
	protected function _getHandlerTypeModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_HandlerType');
	} /* END ThemeHouse_Objects_DataWriter_HandlerType::_getHandlerTypeModel */
}