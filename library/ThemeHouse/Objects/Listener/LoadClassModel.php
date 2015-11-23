<?php

class ThemeHouse_Objects_Listener_LoadClassModel extends ThemeHouse_Listener_LoadClass
{
    /**
     * Gets the classes that are extended for this add-on. See parent for explanation.
     *
     * @return array
     */
    protected function _getExtends()
    {
        return array(
            'XenForo_Model_AddOn' => 'ThemeHouse_Objects_Extend_XenForo_Model_AddOn',
            'XenForo_Model_User' => 'ThemeHouse_Objects_Extend_XenForo_Model_User',
        );
    } /* END ThemeHouse_Objects_Listener_LoadClassModel::_getExtends */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_Objects_Listener_LoadClassModel($class, $extend);
        $extend = $loadClassModel->run();
    } /* END ThemeHouse_Objects_Listener_LoadClassModel::loadClassModel */
}