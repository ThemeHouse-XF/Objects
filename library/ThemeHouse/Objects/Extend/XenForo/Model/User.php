<?php

/**
 * Model for users.
 *
 * @see XenForo_Model_User
 */
class ThemeHouse_Objects_Extend_XenForo_Model_User extends XFCP_ThemeHouse_Objects_Extend_XenForo_Model_User
{
    /**
     * Determines if the viewing user can view handler types.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canViewHandlerTypes(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'viewHandlerTypes'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canViewHandlerTypes */

    /**
     * Determines if the viewing user can create handler types.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canCreateHandlerTypes(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'createHandlerTypes'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canCreateHandlerTypes */

    /**
     * Determines if the viewing user can view handlers.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canViewHandlers(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'viewHandlers'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canViewHandlers */

    /**
     * Determines if the viewing user can create handlers.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canCreateHandlers(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'createHandlers'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canCreateHandlers */

    /**
     * Determines if the viewing user can view classes.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canViewClasses(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'viewClasses'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canViewClasses */

    /**
     * Determines if the viewing user can create classes.
     *
     * @param string $errorPhraseKey By ref. More specific error, if available.
     * @param array|null $viewingUser Viewing user reference
     *
     * @return boolean
     */
    public function canCreateClasses(&$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);
        return ($viewingUser['user_id'] && XenForo_Permission::hasPermission($viewingUser['permissions'], 'thObjects', 'createClasses'));
    } /* END ThemeHouse_Objects_Extend_XenForo_Model_User::canCreateClasses */
}