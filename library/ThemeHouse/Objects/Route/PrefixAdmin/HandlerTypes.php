<?php

/**
 * Route prefix handler for handler types in the admin control panel.
 */
class ThemeHouse_Objects_Route_PrefixAdmin_HandlerTypes implements XenForo_Route_Interface
{
	/**
	 * Match a specific route for an already matched prefix.
	 * 
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithStringParam($routePath, $request, 'handler_type_id');
		$action = $router->resolveActionAsPageNumber($action, $request);
		return $router->getRouteMatch('ThemeHouse_Objects_ControllerAdmin_HandlerType', $action, 'applications');
	} /* END ThemeHouse_Objects_Route_PrefixAdmin_HandlerTypes::match */

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 * 
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		return XenForo_Link::buildBasicLinkWithStringParam($outputPrefix, $action, $extension, $data, 'handler_type_id', 'title');
	} /* END ThemeHouse_Objects_Route_PrefixAdmin_HandlerTypes::buildLink */
}