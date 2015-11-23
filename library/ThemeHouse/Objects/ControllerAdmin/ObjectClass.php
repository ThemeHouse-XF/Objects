<?php

/**
 * Admin controller for handling actions on classes.
 */
class ThemeHouse_Objects_ControllerAdmin_ObjectClass extends XenForo_ControllerAdmin_Abstract
{
	/**
	 * Shows a list of classes.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionIndex()
	{
		$objectClassModel = $this->_getObjectClassModel();
		$objectClasses = $objectClassModel->getObjectClasses();
		$viewParams = array(
			'objectClasses' => $objectClasses,
		);
		return $this->responseView('ThemeHouse_Objects_ViewAdmin_ObjectClass_List', 'th_object_class_list_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::actionIndex */

	/**
	 * Helper to get the class add/edit form controller response.
	 * 
	 * @param array $objectClass
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	protected function _getObjectClassAddEditResponse(array $objectClass)
	{
		$choices = array(
			'class_id' => ThemeHouse_FirstColReports_Model_Class::getClassTitles(),
		);
		$viewParams = array(
			'choices' => $choices,
			'objectClass' => $objectClass,
		);
		
		return $this->responseView('ThemeHouse_Objects_ViewAdmin_ObjectClass_Edit', 'th_object_class_edit_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::_getObjectClassAddEditResponse */

	/**
	 * Displays a form to add a new class.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionAdd()
	{
		$objectClass = $this->_getObjectClassModel()->getDefaultObjectClass();
		
		return $this->_getObjectClassAddEditResponse($objectClass);
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::actionAdd */

	/**
	 * Displays a form to edit an existing class.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionEdit()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		
		if (!$objectClassId)
		{
			return $this->responseReroute('ThemeHouse_Objects_ControllerAdmin_ObjectClass', 'add');
		}
		
		$objectClass = $this->_getObjectClassOrError($objectClassId);
		
		return $this->_getObjectClassAddEditResponse($objectClass);
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::actionEdit */

	/**
	 * Inserts a new class or updates an existing one.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionSave()
	{
		$this->_assertPostOnly();
		
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		
		$input = $this->_input->filter(array(
			'title' => XenForo_Input::STRING,
			'addon_id' => XenForo_Input::STRING,
			'class_id' => XenForo_Input::STRING,
		));
		
		$writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_ObjectClass');
		if ($objectClassId)
		{
			$objectClass = $this->_getObjectClassOrError($objectClassId);
			$writer->setExistingData($objectClass);
		}
		else
		{
		}
		$writer->bulkSet($input);
		$writer->save();
		
		if ($this->_input->filterSingle('reload', XenForo_Input::STRING))
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_UPDATED,
				XenForo_Link::buildAdminLink('classes/edit', $writer->getMergedData())
			);
		}
		else
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				XenForo_Link::buildAdminLink('classes') . $this->getLastHash($writer->get('object_class_id'))
			);
		}
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::actionSave */

	/**
	 * Deletes a class.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionDelete()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		$objectClass = $this->_getObjectClassOrError($objectClassId);
		
		$writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_ObjectClass');
		$writer->setExistingData($objectClass);
		
		if ($this->isConfirmedPost()) // delete class
		{
			$writer->delete();
		
			return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildAdminLink('classes')
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
				'objectClass' => $objectClass
			);
		
			return $this->responseView('ThemeHouse_Objects_ViewAdmin_ObjectClass_Delete', 'th_object_class_delete_objects', $viewParams);
		}
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::actionDelete */

	/**
	 * Gets a valid class or throws an exception.
	 * 
	 * @param string $objectClassId
	 * 
	 * @return array
	 */
	protected function _getObjectClassOrError($objectClassId)
	{
		$objectClass = $this->_getObjectClassModel()->getObjectClassById($objectClassId);
		if (!$objectClass)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('th_requested_object_class_not_found_objects'), 404));
		}
		
		return $objectClass;
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::_getObjectClassOrError */

	/**
	 * Get the classes model.
	 * 
	 * @return ThemeHouse_Objects_Model_ObjectClass
	 */
	protected function _getObjectClassModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_ObjectClass');
	} /* END ThemeHouse_Objects_ControllerAdmin_ObjectClass::_getObjectClassModel */
}