<?php

/**
 * Data writer for classes.
 */
class ThemeHouse_Objects_DataWriter_Class extends XenForo_DataWriter
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
                'class_id' => array('type' => self::TYPE_STRING, 'maxLength' => 50, 'required' => true, 'verification' => array('$this', '_verifyClassId'), 'requiredError' => 'please_enter_valid_class_id'), 
                'title' => array('type' => self::TYPE_STRING, 'required' => true), 
                'addon_id' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'parent' => array('type' => self::TYPE_UINT, 'default' => 0), 
                'class_id_plural' => array('type' => self::TYPE_STRING, 'maxLength' => 50, 'default' => ''), 
                'title_plural' => array('type' => self::TYPE_STRING, 'default' => ''), 
                'route_prefix' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'route_prefix_admin' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'permissions' => array('type' => self::TYPE_SERIALIZED, 'default' => ''), 
                'handler_cache' => array('type' => self::TYPE_SERIALIZED, 'default' => ''), 
                'per_page' => array('type' => self::TYPE_UINT, 'default' => 0), 
                'datawriter_options' => array('type' => self::TYPE_SERIALIZED, 'default' => ''), 
                'major_section' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'tab_name' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'is_abstract' => array('type' => self::TYPE_BOOLEAN, 'default' => 0), 
                'extend' => array('type' => self::TYPE_UINT, 'default' => 0), 
                'permission_group_id' => array('type' => self::TYPE_STRING, 'maxLength' => 25, 'default' => ''), 
                'interface_group_id' => array('type' => self::TYPE_STRING, 'maxLength' => 50, 'default' => ''), 
                'moderator_interface_group_id' => array('type' => self::TYPE_STRING, 'maxLength' => 50, 'default' => ''), 
            	'title_full' => array('type' => self::TYPE_STRING, 'default' => ''),  
            	'table_name' => array('type' => self::TYPE_STRING, 'default' => ''),  
            	'primary_key_id' => array('type' => self::TYPE_STRING, 'default' => ''),  
            ), 
        );
    } /* END ThemeHouse_Objects_DataWriter_Class::_getFields */

    /**
     * Gets the actual existing data out of data that was passed in. See parent for explanation.
     *
     * @param mixed
     *
     * @return array|false
     */
    protected function _getExistingData($data)
    {
        if (!$objectClassId = $this->_getExistingPrimaryKey($data, 'object_class_id'))
        {
            return false;
        }

        $class = $this->_getClassModel()->getClassById($objectClassId);
        if (!$class)
        {
            return false;
        }

        return $this->getTablesDataFromArray($class);
    } /* END ThemeHouse_Objects_DataWriter_Class::_getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'object_class_id = ' . $this->_db->quote($this->getExisting('object_class_id'));
    } /* END ThemeHouse_Objects_DataWriter_Class::_getUpdateCondition */

    /**
     * Verifies that the ID contains valid characters and does not already exist.
     *
     * @param $id
     *
     * @return boolean
     */
    protected function _verifyClassId(&$id)
    {
        if (preg_match('/[^a-zA-Z0-9_]/', $id))
        {
            $this->error(new XenForo_Phrase('please_enter_an_id_using_only_alphanumeric'), 'class_id');
            return false;
        }

        if ($id !== $this->getExisting('class_id') && $this->_getClassModel()->getClassById($id))
        {
            $this->error(new XenForo_Phrase('class_ids_must_be_unique'), 'class_id');
            return false;
        }

        return true;
    } /* END ThemeHouse_Objects_DataWriter_Class::_verifyClassId */

    /**
     * Gets the data writer's default options.
     *
     * @return array
     */
    protected function _getDefaultOptions()
    {
    } /* END ThemeHouse_Objects_DataWriter_Class::_getDefaultOptions */

    /**
     * Get the classes model.
     *
     * @return ThemeHouse_Objects_Model_Class
     */
    protected function _getClassModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Class');
    } /* END ThemeHouse_Objects_DataWriter_Class::_getClassModel */
}