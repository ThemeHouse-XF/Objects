<?php

class ThemeHouse_Objects_Listener_ControllerPreDispatch extends ThemeHouse_Listener_ControllerPreDispatch
{
	public static function controllerPreDispatch(XenForo_Controller $controller, $action)
	{
		$controllerPreDispatch = new ThemeHouse_Objects_Listener_ControllerPreDispatch($controller, $action);
		$controllerPreDispatch->run();
	} /* END ThemeHouse_Objects_Listener_ControllerPreDispatch::controllerPreDispatch */
}