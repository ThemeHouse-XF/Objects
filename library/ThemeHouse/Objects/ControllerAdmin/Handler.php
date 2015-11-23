<?php

/**
 * Admin controller for handling actions on handlers.
 */
class ThemeHouse_Objects_ControllerAdmin_Handler extends XenForo_ControllerAdmin_Abstract
{
    /**
     * Shows a list of handlers.
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionIndex()
    {
        $handlerModel = $this->_getHandlerModel();
        $handlers = $handlerModel->getHandlers();
        $viewParams = array(
            'handlers' => $handlers,
        );
        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Handler_List', 'th_handler_list_objects', $viewParams);
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionIndex */

    /**
     * Helper to get the handler add/edit form controller response.
     *
     * @param array $handler
     *
     * @return XenForo_ControllerResponse_View
     */
    protected function _getHandlerAddEditResponse(array $handler)
    {
        $choices = array(
            'handler_type_id' => ThemeHouse_Objects_Model_HandlerType::getHandlerTypeTitles(),
            'object_class_id' => ThemeHouse_Objects_Model_Class::getClassTitles(),
        );
        $viewParams = array(
            'handler' => $handler,
            'choices' => $choices,
        );

        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Handler_Edit', 'th_handler_edit_objects', $viewParams);
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::_getHandlerAddEditResponse */

    /**
     * Prompt the user to choose the handler type they would like to add
     *
     * @return XenForo_ControllerResponse_View
     */
    public function actionAdd()
    {
        $handlerTypeModel = $this->_getHandlerTypeModel();

        $viewParams = array(
            'handlerTypeOptions' => ThemeHouse_Objects_Model_HandlerType::getHandlerTypeTitles(),
        );

        return $this->responseView('ThemeHouse_Objects_ViewAdmin_Handler_Add', 'th_handler_add_objects', $viewParams);
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionAdd */

    /**
     * If one tries to edit a handler, reroute to the controller appropriate to its type
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionEdit()
    {
        $handlerId = $this->_input->filterSingle('handler_id', XenForo_Input::STRING);

        if ($handlerId && $handlerType = $this->_getHandlerModel()->getHandlerTypeByHandlerId($handlerId))
        {
            return $this->responseReroute($handlerType['controller_admin_class'], 'edit');
        }
        else
        {
            return $this->responseError(new XenForo_Phrase('th_requested_handler_not_found_objects'), 404);
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionEdit */

    /**
     * Accept a form input from actionAdd and either reroute to the appropriate handler, or fail and exit
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionInsert()
    {
        $handlerTypeId = $this->_input->filterSingle('handler_type_id', XenForo_Input::STRING);

        if ($handlerTypeId && $handlerType = $this->_getHandlerTypeModel()->getHandlerTypeById($handlerTypeId))
        {
            return $this->responseReroute($handlerType['controller_admin_class'], 'edit');
        }
        else
        {
            return $this->responseReroute(__CLASS__, 'add');
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionInsert */

    /**
     * Inserts a new handler or updates an existing one.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionSave()
    {
        $handlerTypeId = $this->_input->filterSingle('handler_type_id', XenForo_Input::STRING);
        if ($handlerTypeId && $handlerType = $this->_getHandlerTypeModel()->getHandlerTypeById($handlerTypeId))
        {
            return $this->responseReroute($handlerType['controller_admin_class'], 'save');
        }
        else
        {
            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildAdminLink('handlers')
            );
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionSave */

    /**
     * Deletes a handler.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionDelete()
    {
        $handlerId = $this->_input->filterSingle('handler_id', XenForo_Input::STRING);
        $handler = $this->_getHandlerOrError($handlerId);

        $writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Handler');
        $writer->setExistingData($handler);

        if ($this->isConfirmedPost()) // delete handler
        {
            $writer->delete();

            return $this->responseRedirect(
                    XenForo_ControllerResponse_Redirect::SUCCESS,
                    XenForo_Link::buildAdminLink('handlers')
            );
        }
        else // show delete confirmation prompt
        {
            $writer->preDelete();
            $errors = $writer->getErrors();
            if ($errors)
            {
                return $this->responseError($errors);
            }

            $viewParams = array(
                'handler' => $handler
            );

            return $this->responseView('ThemeHouse_Objects_ViewAdmin_Handler_Delete', 'th_handler_delete_objects', $viewParams);
        }
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionDelete */

    /**
     * Gets a valid handler or throws an exception.
     *
     * @param string $handlerId
     *
     * @return array
     */
    protected function _getHandlerOrError($handlerId)
    {
        $handler = $this->_getHandlerModel()->getHandlerById($handlerId);
        if (!$handler)
        {
            throw $this->responseException($this->responseError(new XenForo_Phrase('th_requested_handler_not_found_objects'), 404));
        }

        return $handler;
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::_getHandlerOrError */

    /**
     * Get the handlers model.
     *
     * @return ThemeHouse_Objects_Model_Handler
     */
    protected function _getHandlerModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_Handler');
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::_getHandlerModel */

    /**
     * Get the handler types model.
     *
     * @return ThemeHouse_Objects_Model_HandlerType
     */
    protected function _getHandlerTypeModel()
    {
        return $this->getModelFromCache('ThemeHouse_Objects_Model_HandlerType');
    } /* END ThemeHouse_Objects_ControllerAdmin_Handler::_getHandlerTypeModel */
}