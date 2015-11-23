<?php

/**
 * Admin controller for handling actions on handler types.
 */
class ThemeHouse_Objects_ControllerAdmin_HandlerType extends XenForo_ControllerAdmin_Abstract
{
	/**
	 * Shows a list of handler types.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionIndex()
	{
		$handlerTypeModel = $this->_getHandlerTypeModel();
		$handlerTypes = $handlerTypeModel->getHandlerTypes();
		$viewParams = array(
			'handlerTypes' => $handlerTypes,
		);
		return $this->responseView('ThemeHouse_Objects_ViewAdmin_HandlerType_List', 'th_handler_type_list_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::actionIndex */

	/**
	 * Helper to get the handler type add/edit form controller response.
	 * 
	 * @param array $handlerType
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	protected function _getHandlerTypeAddEditResponse(array $handlerType)
	{
		$addOnModel = $this->_getAddOnModel();
		
		$viewParams = array(
			'handlerType' => $handlerType,

			'addOnOptions' => $addOnModel->getAddOnOptionsListIfAvailable(),
			'addOnSelected' => (isset($handlerType['addon_id']) ? $handlerType['addon_id'] : $addOnModel->getDefaultAddOnId()),
		);
		
		return $this->responseView('ThemeHouse_Objects_ViewAdmin_HandlerType_Edit', 'th_handler_type_edit_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::_getHandlerTypeAddEditResponse */

	/**
	 * Displays a form to add a new handler type.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionAdd()
	{
		$handlerType = $this->_getHandlerTypeModel()->getDefaultHandlerType();
		
		return $this->_getHandlerTypeAddEditResponse($handlerType);
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::actionAdd */

	/**
	 * Displays a form to edit an existing handler type.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionEdit()
	{
		$handlerTypeId = $this->_input->filterSingle('handler_type_id', XenForo_Input::STRING);
		
		if (!$handlerTypeId)
		{
			return $this->responseReroute('ThemeHouse_Objects_ControllerAdmin_HandlerType', 'add');
		}
		
		$handlerType = $this->_getHandlerTypeOrError($handlerTypeId);
		
		return $this->_getHandlerTypeAddEditResponse($handlerType);
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::actionEdit */

	/**
	 * Inserts a new handler type or updates an existing one.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionSave()
	{
		$this->_assertPostOnly();
		
		$handlerTypeId = $this->_input->filterSingle('handler_type_id', XenForo_Input::STRING);
		
		$input = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'addon_id' => XenForo_Input::STRING,
			'controller_admin_class' => XenForo_Input::STRING,
		));
		
		$writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_HandlerType');
		if ($handlerTypeId)
		{
			$handlerType = $this->_getHandlerTypeOrError($handlerTypeId);
			$writer->setExistingData($handlerType);
		}
		else
		{
			$writer->set('handler_type_id', $this->_input->filterSingle('new_handler_type_id', XenForo_Input::STRING));
		}
		$writer->bulkSet($input);
		$writer->save();
		
		if ($this->_input->filterSingle('reload', XenForo_Input::STRING))
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
				XenForo_Link::buildAdminLink('handler-types/edit', $writer->getMergedData())
			);
		}
		else
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('handler-types') . $this->getLastHash($writer->get('handler_type_id'))
			);
		}
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::actionSave */

	/**
	 * Deletes a handler type.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionDelete()
	{
		$handlerTypeId = $this->_input->filterSingle('handler_type_id', XenForo_Input::STRING);
		$handlerType = $this->_getHandlerTypeOrError($handlerTypeId);
		
		$writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_HandlerType');
		$writer->setExistingData($handlerType);
		
		if ($this->isConfirmedPost()) // delete handler type
		{
			$writer->delete();
		
			return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildAdminLink('handler-types')
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
				'handlerType' => $handlerType
			);
		
			return $this->responseView('ThemeHouse_Objects_ViewAdmin_HandlerType_Delete', 'th_handler_type_delete_objects', $viewParams);
		}
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::actionDelete */

	/**
	 * Gets a valid handler type or throws an exception.
	 * 
	 * @param string $handlerTypeId
	 * 
	 * @return array
	 */
	protected function _getHandlerTypeOrError($handlerTypeId)
	{
		$handlerType = $this->_getHandlerTypeModel()->getHandlerTypeById($handlerTypeId);
		if (!$handlerType)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('th_requested_handler_type_not_found_objects'), 404));
		}
		
		return $handlerType;
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::_getHandlerTypeOrError */

	/**
	 * Get the handler types model.
	 * 
	 * @return ThemeHouse_Objects_Model_HandlerType
	 */
	protected function _getHandlerTypeModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_HandlerType');
	} /* END ThemeHouse_Objects_ControllerAdmin_HandlerType::_getHandlerTypeModel */

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