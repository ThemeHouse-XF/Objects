<?php

class ThemeHouse_Objects_Listener_TemplateHook extends ThemeHouse_Listener_TemplateHook
{
	public function run() {
		return parent::run();
	}
	
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
	{
		$templateHook = new ThemeHouse_Objects_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
		$contents = $templateHook->run();
	}
	

}