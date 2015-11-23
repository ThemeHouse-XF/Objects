<?php

/**
 * Installer for [⌂] Objects.
 */
class ThemeHouse_Objects_Install extends ThemeHouse_Install
{
    /**
     * Gets the tables (with fields) to be created for this add-on. See parent for explanation.
     *
     * @return array Format: [table name] => fields
     */
    protected function _getTables()
    {
        return array(
            'xf_object_class' => array(
                'object_class_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', 
                'class_id' => 'VARCHAR(50) NOT NULL', 
                'title' => 'VARCHAR(255) NOT NULL', 
                'addon_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'parent' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', 
                'title_plural' => 'VARCHAR(150) NOT NULL DEFAULT \'\'', 
                'class_id_plural' => 'VARCHAR(50) NOT NULL DEFAULT \'\'', 
                'route_prefix' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'route_prefix_admin' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'permissions' => 'MEDIUMBLOB NULL', 
                'handler_cache' => 'MEDIUMBLOB NULL', 
                'per_page' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', 
                'datawriter_options' => 'MEDIUMBLOB NULL', 
                'major_section' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'tab_name' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'title_full' => 'VARCHAR(150) NOT NULL DEFAULT \'\'', 
                'table_name' => 'VARCHAR(150) NOT NULL DEFAULT \'\'', 
                'primary_key_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'is_abstract' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT 0', 
                'extend' => 'INT(10) UNSIGNED NOT NULL DEFAULT 0', 
                'permission_group_id' => 'VARCHAR(25) NOT NULL DEFAULT \'\'', 
                'interface_group_id' => 'VARCHAR(50) NOT NULL DEFAULT \'\'', 
                'moderator_interface_group_id' => 'VARCHAR(50) NOT NULL DEFAULT \'\'', 
            ), 
            'xf_object' => array(
                'object_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', 
                'object_class_id' => 'INT(10) UNSIGNED NOT NULL', 
                'title' => 'VARCHAR(150) NOT NULL DEFAULT \'\'', 
                'subtitle' => 'VARCHAR(150) NOT NULL DEFAULT \'\'', 
            ), 
            'xf_handler_type' => array(
                'handler_type_id' => 'VARCHAR(30) NOT NULL PRIMARY KEY', 
                'title' => 'VARCHAR(255) NOT NULL', 
                'addon_id' => 'VARCHAR(30) NOT NULL DEFAULT \'\'', 
                'controller_admin_class' => 'TEXT', 
            ), 
            'xf_handler' => array(
                'handler_id' => 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', 
                'handler_type_id' => 'VARCHAR(255) NOT NULL', 
                'content_type' => 'VARCHAR(255) NOT NULL', 
                'object_class_id' => 'INT(10) UNSIGNED NOT NULL', 
                'extra_data_cache' => 'MEDIUMBLOB NULL', 
            ), 
        );
    } /* END ThemeHouse_Objects_Install::_getTables */

    protected function _getUniqueKeys()
    {
        return array(
            'xf_handler' => array(
                'content_type_handler_type_id' => array('content_type', 'handler_type_id'), 
            ), 
        );
    } /* END ThemeHouse_Objects_Install::_getUniqueKeys */

    protected function _getKeys()
    {
        return array(
            'xf_object' => array(
                'object_class_id' => array('object_class_id'), 
            ), 
        );
    } /* END ThemeHouse_Objects_Install::_getKeys */

    protected function _postInstall()
    {
/*    	$fileTransfer = new Zend_File_Transfer_Adapter_Http();
    	if ($fileTransfer->isUploaded('upload_file'))
    	{
    		$fileInfo = $fileTransfer->getFileInfo('upload_file');
    		$fileName = $fileInfo['upload_file']['tmp_name'];
    	}
    	else
    	{
    		$fileName = $_POST['server_file'];
    	}

		$xml = new SimpleXMLElement($fileName, 0, true);

		$this->getModelFromCache('ThemeHouse_Objects_Model_Class')->importClassesAddOnXml($xml->classes, 'ThemeHouse_Objects');*/
    } /* END ThemeHouse_Objects_Install::_postInstall */
}