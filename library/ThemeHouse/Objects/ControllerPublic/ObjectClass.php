<?php

/**
 * Public controller for handling actions on classes.
 */
class ThemeHouse_Objects_ControllerPublic_ObjectClass extends XenForo_ControllerPublic_Abstract
{
	/**
	 * Shows a list of classes.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionIndex()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::UINT);
		if ($objectClassId)
		{
			return $this->responseReroute(__CLASS__, 'view');
		}
		$this->_assertCanViewObjectClasses();
		
		$objectClassModel = $this->_getObjectClassModel();
		
		$conditions = $this->_getListConditions();
		$fetchOptions = $this->_getListFetchOptions();
		
		$totalObjectClasses = $objectClassModel->countObjectClasses($conditions);
		
		$objectClasses = $objectClassModel->getObjectClasses($conditions, $fetchOptions);
		
		$viewParams = array(
			'objectClasses' => $objectClasses,
			'canCreateObjectClasses' => $this->_getUserModel()->canCreateObjectClasses(),
		);
		return $this->responseView('ThemeHouse_Objects_ViewPublic_ObjectClass_List', 'th_object_class_list_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionIndex */

	protected function _getListConditions()
	{
		return array();
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getListConditions */

	protected function _getListFetchOptions()
	{
		return array();
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getListFetchOptions */

	/**
	 * Displays a class.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionView()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		$objectClass = $this->_getObjectClassOrError($objectClassId);
		
		$this->_assertCanViewObjectClasses();
		
		$viewParams = array(
			'objectClass' => $objectClass,
			'canEditObjectClass' => $this->_getObjectClassModel()->canEditObjectClass($objectClass),
			'canDeleteObjectClass' => $this->_getObjectClassModel()->canDeleteObjectClass($objectClass),
		);
		
		return $this->responseView('ThemeHouse_Objects_ViewPublic_ObjectClass_View', 'th_object_class_view_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionView */

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
		
		if ($objectClass['object_class_id'])
		{
			$viewParams['canDeleteObjectClass'] = $this->_getObjectClassModel()->canDeleteObjectClass($objectClass);
		}
		
		return $this->responseView('ThemeHouse_Objects_ViewPublic_ObjectClass_Edit', 'th_object_class_edit_objects', $viewParams);
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getObjectClassAddEditResponse */

	/**
	 * Displays a form to add a new class.
	 * 
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionAdd()
	{
		$this->_assertCanCreateObjectClasses();
		
		return $this->_getObjectClassAddEditResponse($this->_getObjectClassModel()->getDefaultObjectClass());
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionAdd */

	/**
	 * Displays a form to edit an existing class.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionEdit()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		$objectClass = $this->_getObjectClassOrError($objectClassId);
		
		$this->_assertCanEditObjectClass($objectClass);
		
		return $this->_getObjectClassAddEditResponse($objectClass);
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionEdit */

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
			$this->_assertCanEditObjectClass($objectClass);
			$writer->setExistingData($objectClass);
		}
		else
		{
			$this->_assertCanCreateObjectClasses();
		}
		$writer->bulkSet($input);
		$writer->save();
		
		$objectClass = $writer->getMergedData();
		
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			XenForo_Link::buildPublicLink('classes', $objectClass)
		);
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionSave */

	/**
	 * Deletes a class.
	 * 
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionDelete()
	{
		$objectClassId = $this->_input->filterSingle('object_class_id', XenForo_Input::STRING);
		$objectClass = $this->_getObjectClassOrError($objectClassId);
		
		$this->_assertCanDeleteObjectClass($objectClass);
		
		$writer = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_ObjectClass');
		$writer->setExistingData($objectClass);
		
		if ($this->isConfirmedPost()) // delete class
		{
			$writer->delete();
		
			return $this->responseRedirect(
					XenForo_ControllerResponse_Redirect::SUCCESS,
					XenForo_Link::buildPublicLink('classes')
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
		
			return $this->responseView('ThemeHouse_Objects_ViewPublic_ObjectClass_Delete', 'th_object_class_delete_objects', $viewParams);
		}
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::actionDelete */

	/**
	 * Asserts that the currently browsing user can view classes.
	 */
	protected function _assertCanViewObjectClasses()
	{
		if (!$this->_getUserModel()->canViewObjectClasses($errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_assertCanViewObjectClasses */

	/**
	 * Asserts that the currently browsing user can create classes.
	 */
	protected function _assertCanCreateObjectClasses()
	{
		if (!$this->_getUserModel()->canCreateObjectClasses($errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_assertCanCreateObjectClasses */

	/**
	 * Asserts that the currently browsing user can edit the specified class.
	 * 
	 * @param array $objectClass
	 */
	protected function _assertCanEditObjectClass(array $objectClass)
	{
		if (!$this->_getObjectClassModel()->canEditObjectClass($objectClass, $errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_assertCanEditObjectClass */

	/**
	 * Asserts that the currently browsing user can delete the specified class.
	 * 
	 * @param array $objectClass
	 */
	protected function _assertCanDeleteObjectClass(array $objectClass)
	{
		if (!$this->_getObjectClassModel()->canDeleteObjectClass($objectClass, $errorPhraseKey))
		{
			throw $this->getErrorOrNoPermissionResponseException($errorPhraseKey);
		}
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_assertCanDeleteObjectClass */

	/**
	 * Session activity details.
	 * @see XenForo_Controller::getSessionActivityDetailsForList()
	 */
	public static function getSessionActivityDetailsForList(array $activities)
	{
		return new XenForo_Phrase('th_viewing_object_classes_objects');
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::getSessionActivityDetailsForList */

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
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getObjectClassOrError */

	/**
	 * Get the classes model.
	 * 
	 * @return ThemeHouse_Objects_Model_ObjectClass
	 */
	protected function _getObjectClassModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_ObjectClass');
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getObjectClassModel */

	/**
	 * Get the users model.
	 * 
	 * @return XenForo_Model_User
	 */
	protected function _getUserModel()
	{
		return $this->getModelFromCache('XenForo_Model_User');
	} /* END ThemeHouse_Objects_ControllerPublic_ObjectClass::_getUserModel */
}