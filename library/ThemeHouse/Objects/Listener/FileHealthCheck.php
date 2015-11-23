<?php

class ThemeHouse_Objects_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/Objects/ControllerAdmin/Class.php' => '0bdbde3456ed807aa1af05bd2db2275a',
                'library/ThemeHouse/Objects/ControllerAdmin/Handler.php' => '9a583b3c90297665549bcdccce72add3',
                'library/ThemeHouse/Objects/ControllerAdmin/HandlerAbstract.php' => '6761162dcd48f7057c7049fc7e82e8a7',
                'library/ThemeHouse/Objects/ControllerAdmin/HandlerType.php' => '679f1ae8ec586eaae6d2e0556e7d8f0c',
                'library/ThemeHouse/Objects/ControllerAdmin/Object.php' => '79bf094f16889d3e0eae646fde21a64c',
                'library/ThemeHouse/Objects/ControllerAdmin/ObjectClass.php' => 'eb8c624313f5ad54a83ae55d74e69178',
                'library/ThemeHouse/Objects/ControllerPublic/Class.php' => '4ad55d8434da3243c99c3137e22ff982',
                'library/ThemeHouse/Objects/ControllerPublic/Object.php' => '9804004a0514d618662684c4f2aa8625',
                'library/ThemeHouse/Objects/ControllerPublic/ObjectClass.php' => '2fee5b868adc485c99f78564f917e330',
                'library/ThemeHouse/Objects/DataWriter/Class.php' => '39f00a7eec3779d17a82e6dd10142cb1',
                'library/ThemeHouse/Objects/DataWriter/Handler.php' => 'b0e8d6057b44da8e3dd94c2d1bae6590',
                'library/ThemeHouse/Objects/DataWriter/HandlerType.php' => '53ff4254da84b497276ca96ef161b1fa',
                'library/ThemeHouse/Objects/DataWriter/Object.php' => '5418f6c4e42cc56334a908dd71dadc23',
                'library/ThemeHouse/Objects/DataWriter/ObjectClass.php' => 'fbaabfcec1f9dc77408f690a72f40f72',
                'library/ThemeHouse/Objects/Extend/XenForo/Model/AddOn.php' => 'd75866222377eb6c2ae21f38596224c2',
                'library/ThemeHouse/Objects/Extend/XenForo/Model/User.php' => '470c31f9b301a9d2879ec505406d7ef0',
                'library/ThemeHouse/Objects/Install/Controller.php' => 'a3066d049a79eeed51566d09ca287b48',
                'library/ThemeHouse/Objects/Install.php' => 'fe8989bb6b44959b9bb56cf7da9b696e',
                'library/ThemeHouse/Objects/Listener/ControllerPreDispatch.php' => 'a8bbd3be228a82ddad23dce4c95c618b',
                'library/ThemeHouse/Objects/Listener/InitDependencies.php' => '8cc33cba27354f59264c8668076757f0',
                'library/ThemeHouse/Objects/Listener/LoadClassModel.php' => '8b3db55b8d08c1a4cbe86fa5e1df3559',
                'library/ThemeHouse/Objects/Listener/TemplateHook.php' => '632448859116d96db0c549d523103af1',
                'library/ThemeHouse/Objects/Model/Class.php' => '7992e369c624bf198932c3ff890a9688',
                'library/ThemeHouse/Objects/Model/Handler.php' => '7975932e0b73a46fa99f8af49deaf709',
                'library/ThemeHouse/Objects/Model/HandlerType.php' => 'cb04129a6ac63b694cc7ecd2513a5ae7',
                'library/ThemeHouse/Objects/Model/Object.php' => 'f4ff7af42a2c50ed90df1245c38c2d2a',
                'library/ThemeHouse/Objects/Model/ObjectClass.php' => 'd7efbe63f6d710eda726c9d124ce0549',
                'library/ThemeHouse/Objects/Route/Prefix/Classes.php' => '2a062d6e66317e87891fe4f21db7dc5f',
                'library/ThemeHouse/Objects/Route/Prefix/Objects.php' => 'a2bad89f2e7b2869800c3838ce1e312f',
                'library/ThemeHouse/Objects/Route/PrefixAdmin/Classes.php' => '52448173026a26535e6b2559b832d43f',
                'library/ThemeHouse/Objects/Route/PrefixAdmin/Handlers.php' => '6bdeab7331f5e17f36acb17c7fb7cc2e',
                'library/ThemeHouse/Objects/Route/PrefixAdmin/HandlerTypes.php' => 'fecf163fa52585910e280c0708c29b79',
                'library/ThemeHouse/Objects/Route/PrefixAdmin/Objects.php' => '3154137497d3c903485ac3bcacc9e022',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplateHook.php' => 'a767a03baad0ca958d19577200262d50',
                'library/ThemeHouse/Listener/TemplateHook/20150106.php' => '71c539920a651eef3106e19504048756',
            ));
    }
}