<?php

/**
 * Public controller for handling actions on objects.
 */
class ThemeHouse_Objects_ControllerPublic_Object extends XenForo_ControllerPublic_Abstract
{
	/**
	 * Displays a form to edit an existing object.
	 *
	 * @return XenForo_ControllerResponse_View
	 */
	public function actionEdit()
	{
		$objectId = $this->_input->filterSingle('object_id', XenForo_Input::STRING);
		$object = $this->_getObjectOrError($objectId);
		
		$class = $this->_getClassModel()->getClassById($object['class_id']);
		
		$viewParams = array(
			'object' => $object,
			'class' => $class
		);
	
		return $this->responseView('ThemeHouse_Objects_ViewPublic_Object_Edit', 'object_edit', $viewParams);
	}
	
	/**
	 * Inserts a new object or updates an existing one.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionSave()
	{
		$this->_assertPostOnly();

		$objectId = $this->_input->filterSingle('object_id', XenForo_Input::STRING);
		
		$dwInput = $this->_input->filter(array(
			'class_id' => XenForo_Input::STRING,
			'title' => XenForo_Input::STRING,
		));
	
		$dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Object');
		if ($objectId)
		{
			$object = $this->_getObjectOrError($objectId);
			$dw->setExistingData($object);
		}
		else
		{
			$dw->set('class_id', $dwInput['class_id']);
		}
		unset($dwInput['class_id']);
		$dw->bulkSet($dwInput);
		$dw->save();
	
		// TODO: Where should this redirect to?
		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			''
		);
	}
	
	/**
	 * Deletes an object.
	 *
	 * @return XenForo_ControllerResponse_Abstract
	 */
	public function actionDelete()
	{
		$objectId = $this->_input->filterSingle('object_id', XenForo_Input::STRING);
		$object = $this->_getObjectOrError($objectId);
	
		$dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Object');
		$dw->setExistingData($objectId);
	
		if ($this->isConfirmedPost()) // delete class
		{
			$dw->delete();

			// TODO: Where should this redirect to?
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::SUCCESS,
				''
			);
		}
		else // show delete confirmation prompt
		{
			$dw->preDelete();
			$errors = $dw->getErrors();
			if ($errors)
			{
				return $this->responseError($errors);
			}
	
			$viewParams = array(
				'object' => $object
			);
	
			return $this->responseView('ThemeHouse_Objects_ViewPublic_Object_Delete', 'object_delete', $viewParams);
		}
	}
	
	/**
	 * Gets a valid object or throws an exception.
	 *
	 * @param string $objectId
	 *
	 * @return array
	 */
	protected function _getObjectOrError($objectId)
	{
		$info = $this->_getObjectModel()->getObjectById($objectId);
		if (!$info)
		{
			throw $this->responseException($this->responseError(new XenForo_Phrase('requested_object_not_found'), 404));
		}
	
		return $info;
	}
	
	/**
	 * Get the objects model.
	 *
	 * @return ThemeHouse_Objects_Model_Object
	 */
	protected function _getObjectModel()
	{
		return $this->getModelFromCache('ThemeHouse_Objects_Model_Object');
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