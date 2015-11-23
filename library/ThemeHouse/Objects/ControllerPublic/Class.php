<?php

/**
 * Public controller for handling actions on classes.
 */
class ThemeHouse_Objects_ControllerPublic_Class extends XenForo_ControllerPublic_Abstract
{
    /**
     * Displays a form to create a new object.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $classId = $this->_input->filterSingle('class_id', XenForo_Input::STRING);

        $class = $this->_getClassOrError($classId);

        $viewParams = array(
                'class' => $class,
                'object' => array()
        );

        return $this->responseView('ThemeHouse_Objects_ViewPublic_Object_Edit', 'object_edit', $viewParams);
    }

    /**
     * Gets a valid class or throws an exception.
     *
     * @param string $classId
     *
     * @return array
     */
    protected function _getClassOrError($classId)
    {
        $info = $this->_getClassModel()->getClassById($classId);
        if (!$info)
        {
            throw $this->responseException($this->responseError(new XenForo_Phrase('requested_class_not_found'), 404));
        }

        return $info;
    }

    /**
     * Get the classes model.
     *
     * @return ThemeHouse_Objects_Model_Class
     */
    protected function _getClassModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Class');
    }
}