<?php

class ThemeHouse_Objects_Listener_InitDependencies extends ThemeHouse_Listener_InitDependencies
{

    public function run()
    {
        /* @var $classModel ThemeHouse_Objects_Model_Class */
        $classModel = XenForo_Model::create('ThemeHouse_Objects_Model_Class');

        $classes = $classModel->getAllClasses();

        if (self::$_dependencies instanceof XenForo_Dependencies_Public) {
            $routes = self::$_data['routesPublic'];
        } elseif (self::$_dependencies instanceof XenForo_Dependencies_Admin) {
            $routes = self::$_data['routesAdmin'];
        }

        foreach ($classes as $class) {
            $routePrefix = '';
            if (self::$_dependencies instanceof XenForo_Dependencies_Public) {
                $routePrefix = (isset($class['route_prefix']) ? $class['route_prefix'] : '');
            } elseif (self::$_dependencies instanceof XenForo_Dependencies_Admin) {
                $routePrefix = (isset($class['route_prefix_admin']) ? $class['route_prefix_admin'] : '');
            }
            if ($routePrefix && !isset($routes[$routePrefix])) {
                $routes[$routePrefix]['build_link'] = 'all';
                if (self::$_dependencies instanceof XenForo_Dependencies_Public) {
                    $routes[$routePrefix]['route_class'] = 'ThemeHouse_Objects_Route_Prefix_Objects';
                } elseif (self::$_dependencies instanceof XenForo_Dependencies_Admin) {
                    $routes[$routePrefix]['route_class'] = 'ThemeHouse_Objects_Route_PrefixAdmin_Objects';
                }
            }
        }
        if (self::$_dependencies instanceof XenForo_Dependencies_Public) {
            XenForo_Link::setHandlerInfoForGroup('public', $routes);
        } elseif (self::$_dependencies instanceof XenForo_Dependencies_Admin) {
            XenForo_Link::setHandlerInfoForGroup('admin', $routes);
        }

        parent::run();
    }

    public static function initDependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        $initDependencies = new ThemeHouse_Objects_Listener_InitDependencies($dependencies, $data);
        $initDependencies->run();
    }
}