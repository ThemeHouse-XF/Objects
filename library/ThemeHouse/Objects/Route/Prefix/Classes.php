<?php

/**
 * Route prefix handler for classes in the public system.
 */
class ThemeHouse_Objects_Route_Prefix_Classes implements XenForo_Route_Interface
{
    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $action = $router->resolveActionWithIntegerParam($routePath, $request, 'object_class_id');
        $action = $router->resolveActionAsPageNumber($action, $request);
        return $router->getRouteMatch('ThemeHouse_Objects_ControllerPublic_Class', $action, 'forums');
    } /* END ThemeHouse_Objects_Route_Prefix_Classes::match */

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'object_class_id', 'title');
    } /* END ThemeHouse_Objects_Route_Prefix_Classes::buildLink */
}