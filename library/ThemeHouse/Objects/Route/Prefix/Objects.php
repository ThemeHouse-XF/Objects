<?php

/**
 * Route prefix handler for objects in the public system.
 */
class ThemeHouse_Objects_Route_Prefix_Objects implements XenForo_Route_Interface
{
	/**
	 * Match a specific route for an already matched prefix.
	 *
	 * @see XenForo_Route_Interface::match()
	 */
	public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
	{
		$action = $router->resolveActionWithIntegerParam($routePath, $request, 'object_id');
		return $router->getRouteMatch('ThemeHouse_Objects_ControllerPublic_Object', $action, 'forums');
	}

	/**
	 * Method to build a link to the specified page/action with the provided
	 * data and params.
	 *
	 * @see XenForo_Route_BuilderInterface
	 */
	public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
	{
		/* @var $classModel ThemeHouse_Objects_Model_Class */
		$classModel = XenForo_Model::create('ThemeHouse_Objects_Model_Class');
		 
		$classes = $classModel->getAllClasses();
		
		$classId = str_replace("-", "_", $originalPrefix);
		
		if (array_key_exists($classId, $classes))
		{
			return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, $classId.'_id', 'title');
		}
		return XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, 'object_id', 'title');
	}
}