<?php

/**
 * Admin controller for handling actions on handlers.
 */
abstract class ThemeHouse_Objects_ControllerAdmin_HandlerAbstract extends XenForo_ControllerAdmin_Abstract
{
	/**
	 * @return ThemeHouse_Objects_DataWriter_Handler
	 */
	abstract protected function _getHandlerDataWriter();
	
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
		);
		$viewParams = array(
			'choices' => $choices,
			'handler' => $handler,
		);
		
		return $this->responseView('ThemeHouse_Objects_ViewAdmin_Handler_Edit', 'th_handler_edit_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerAdmin_Handler::_getHandlerAddEditResponse */

	/**
	 * Displays a form to add a new handler.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionAdd()
	{
		$handler = $this->_getHandlerModel()->getDefaultHandler();
		
		return $this->_getHandlerAddEditResponse($handler);
	} /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionAdd */

	/**
	 * Displays a form to edit an existing handler.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionEdit()
	{
		$handlerId = $this->_input->filterSingle('handler_id', XenForo_Input::STRING);
		
		if (!$handlerId)
		{
			return $this->responseReroute(__CLASS__, 'add');
		}
		
		$handler = $this->_getHandlerOrError($handlerId);
		
		return $this->_getHandlerAddEditResponse($handler);
	} /* END ThemeHouse_Objects_ControllerAdmin_Handler::actionEdit */

	/**
	 * Inserts a new handler or updates an existing one.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionSave()
	{
		$this->_assertPostOnly();
		
		$handlerId = $this->_input->filterSingle('handler_id', XenForo_Input::STRING);
		
		$input = $this->_input->filter(array(
			'content_type' => XenForo_Input::STRING,
			'handler_type_id' => XenForo_Input::STRING,
		));
		
		$writer = $this->_getNodeDataWriter();
		
		if ($handlerId)
		{
			$handler = $this->_getHandlerOrError($handlerId);
			$writer->setExistingData($handler);
		}

		$writer->bulkSet($input);
		$writer->save();
		
		if ($this->_input->filterSingle('reload', XenForo_Input::STRING))
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
				XenForo_Link::buildAdminLink('handlers/edit', $writer->getMergedData())
			);
		}
		else
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('handlers') . $this->getLastHash($writer->get('handler_id'))
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
		
		$writer = $this->_getHandlerDataWriter();
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
	 * Get the add-ons model.
	 *
	 * @return XenForo_Model_AddOn
	 */
	protected function _getAddOnModel()
	{
		return $this->getModelFromCache('XenForo_Model_AddOn');
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::_getAddOnModel */
}