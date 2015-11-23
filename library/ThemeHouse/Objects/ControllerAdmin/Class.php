<?php

/**
 * Admin controller for handling actions on classes.
 */
class ThemeHouse_Objects_ControllerAdmin_Class extends XenForo_ControllerAdmin_Abstract
{

    /**
     * Shows a list of classes.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionIndex()
    {
        $classModel = $this->_getClassModel();
        $addOnModel = $this->_getAddOnModel();

        $addOns = $addOnModel->getAllAddOns();

        $addOnTitles = array();
        foreach ($addOns as $addOnId => $addOn) {
            $addOnTitles[$addOnId] = $addOn['title'];
        }

        $classCount = 0;
        $addOns = $classModel->getClassesByAddOns(array(), array(), $classCount);

        $viewParams = array(
            'addOns' => $addOns,
            'classCount' => $classCount,
            'addOnTitles' => $addOnTitles
        );

        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Class_List', 'class_list', $viewParams);
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionIndex */

    /**
     * Shows a list of objects.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionList()
    {
        $objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
        $class = $this->_getClassOrError($objectClassId);

        $objectModel = $this->_getObjectModel();

        $objects = $objectModel->getObjects(array(
            'object_class_id' => $objectClassId
        ));

        $viewParams = array(
            'class' => $class,
            'objects' => $objects
        );

        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Object_List', 'object_list', $viewParams);
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionList */

    protected function _getClassAddEditResponse(array $class)
    {
        $addOnModel = $this->_getAddOnModel();
        $permissionModel = $this->_getPermissionModel();

        $viewParams = array(
            'class' => $class,
            'classes' => $this->_getClassModel()->getAllClasses(),

            'addOnOptions' => $addOnModel->getAddOnOptionsListIfAvailable(),
            'addOnSelected' => (isset($class['addon_id']) ? $class['addon_id'] : $addOnModel->getDefaultAddOnId()),
        );

        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Class_Edit', 'class_edit', $viewParams);
    }

    /**
     * Displays a form to create a new class or object.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $classId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
        if ($classId) {
            $class = $this->_getClassOrError($classId);

            $viewParams = array(
                'class' => $class,
                'object' => array()
            );

            return $this->responseView('ThemeHouse_Objects_ViewAdmin_Object_Edit', 'object_edit', $viewParams);
        } else {
            $class = array(
                'addon_id' => ''
            );

            return $this->_getClassAddEditResponse($class);
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionAdd */

    /**
     * Displays a form to edit an existing class.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionEdit()
    {
        $objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
        $class = $this->_getClassOrError($objectClassId);

        if (!empty($class['permissions'])) {
            $class['permissions'] = unserialize($class['permissions']);
        }
        if (empty($class['permissions'])) {
            $class['permissions'] = array();
        }

        if (empty($class['permissions']['public']) && !$class['route_prefix']) {
            $class['route_prefix'] = str_replace('_', '-', $class['class_id_plural']);
        }

        if (empty($class['permissions']['admin']) && !$class['route_prefix_admin']) {
            $class['route_prefix_admin'] = str_replace('_', '-', $class['class_id_plural']);
        }

        return $this->_getClassAddEditResponse($class);
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionEdit */

    /**
     * Inserts a new class or updates an existing one.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $this->_assertPostOnly();

        $objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);

        $dwInput = $this->_input->filter(
            array(
                'class_id' => XenForo_Input::STRING,
                'title' => XenForo_Input::STRING,
                'addon_id' => XenForo_Input::STRING,
                'route_prefix' => XenForo_Input::STRING,
                'route_prefix_admin' => XenForo_Input::STRING,
                'class_id_plural' => XenForo_Input::STRING,
                'title_plural' => XenForo_Input::STRING
            ));
        $dwInput['permissions'] = $this->_input->filterSingle('permissions', XenForo_Input::ARRAY_SIMPLE,
            array(
                'array' => true
            ));

        $dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Class');
        if ($objectClassId) {
            $class = $this->_getClassOrError($objectClassId);
            $dw->setExistingData($class);
        }
        $dw->bulkSet($dwInput);
        $dw->save();

        $class = $dw->getMergedData();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildAdminLink('classes') . $this->getLastHash($class['object_class_id']));
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionSave */

    /**
     * Deletes an class.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        $classId = $this->_input->filterSingle('class_id', XenForo_Input::STRING);
        $class = $this->_getClassOrError($classId);

        $dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Class');
        $dw->setExistingData($classId);

        if ($this->isConfirmedPost()) {
            $dw->delete();
        } else {
            $dw->preDelete();
            $errors = $dw->getErrors();
            if ($errors) {
                return $this->responseError($errors);
            }

            $viewParams = array(
                'class' => $class
            );

            return $this->responseView('ThemeHouse_Objects_ViewAdmin_Class_Delete', 'class_delete', $viewParams);
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::actionDelete */

    /**
     * Gets a valid class or throws an exception.
     *
     * @param string $classId
     *
     * @return array
     */
    protected function _getClassOrError($objectClassId)
    {
        $info = $this->_getClassModel()->getClassById($objectClassId);
        if (!$info) {
            throw $this->responseException($this->responseError(new XenForo_Phrase('requested_class_not_found'), 404));
        }

        return $info;
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::_getClassOrError */

    /**
     * Get the objects model.
     *
     * @return ThemeHouse_Objects_Model_Object
     */
    protected function _getObjectModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Object');
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::_getObjectModel */

    /**
     * Get the classes model.
     *
     * @return ThemeHouse_Objects_Model_Class
     */
    protected function _getClassModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Class');
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::_getClassModel */

    /**
     * Gets the permission model.
     *
     * @return XenForo_Model_Permission
     */
    protected function _getPermissionModel()
    {
        return $this->getModelFromCache('XenForo_Model_Permission');
    }

    /**
     * Get the add-on model.
     *
     * @return XenForo_Model_AddOn
     */
    protected function _getAddOnModel()
    {
        return $this->getModelFromCache('XenForo_Model_AddOn');
    } /* END ThemeHouse_Objects_ControllerAdmin_Class::_getAddOnModel */
}