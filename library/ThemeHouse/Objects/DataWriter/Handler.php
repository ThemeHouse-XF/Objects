<?php

/**
 * Data writer for handlers.
 */
class ThemeHouse_Objects_DataWriter_Handler extends XenForo_DataWriter
{
    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'th_requested_handler_not_found_objects';

    /**
     * Gets the fields that are defined for the table. See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_handler' => array(
                'handler_id' => array('type' => self::TYPE_UINT, 'autoIncrement' => true), 
                'content_type' => array('type' => self::TYPE_STRING, 'maxLength' => 255, 'required' => true), 
                'handler_type_id' => array('type' => self::TYPE_STRING, 'required' => true), 
                'object_class_id' => array('type' => self::TYPE_UINT, 'required' => true), 
                'extra_data_cache' => array('type' => self::TYPE_SERIALIZED, 'default' => ''), 
            ), 
        );
    } /* END ThemeHouse_Objects_DataWriter_Handler::_getFields */

    /**
     * Gets the actual existing data out of data that was passed in. See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$handlerId = $this->_getExistingPrimaryKey($data, 'handler_id'))
        {
            return false;
        }

        $handler = $this->_getHandlerModel()->getHandlerById($handlerId);
        if (!$handler)
        {
            return false;
        }

        return $this->getTablesDataFromArray($handler);
    } /* END ThemeHouse_Objects_DataWriter_Handler::_getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'handler_id = ' . $this->_db->quote($this->getExisting('handler_id'));
    } /* END ThemeHouse_Objects_DataWriter_Handler::_getUpdateCondition */

    /**
     * Gets the data writer's default options.
     *
     * @return array
     */
    protected function _getDefaultOptions()
    {
    } /* END ThemeHouse_Objects_DataWriter_Handler::_getDefaultOptions */

    protected function _postSave()
    {
        $handlers = $this->_getHandlerModel()->getHandlers(array('object_class_id' => $this->get('object_class_id')));

        $handlerCache = array();
        foreach ($handlers as $handler) {
            $handler['extra_data_cache'] = ($handler['extra_data_cache'] ? unserialize($handler['extra_data_cache']) : array());
            $handlerCache[$handler['handler_type_id']][$handler['content_type']] = $handler['extra_data_cache'];
        }

        /* @var $dw ThemeHouse_Objects_DataWriter_Class */
        $dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Class');
        $dw->setExistingData($this->get('object_class_id'));
        $dw->set('handler_cache', $handlerCache);
        $dw->save();
    } /* END ThemeHouse_Objects_DataWriter_Handler::_postSave */

    /**
     * Get the handlers model.
     *
     * @return ThemeHouse_Objects_Model_Handler
     */
    protected function _getHandlerModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Handler');
    } /* END ThemeHouse_Objects_DataWriter_Handler::_getHandlerModel */
}