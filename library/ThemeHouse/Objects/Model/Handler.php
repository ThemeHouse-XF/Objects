<?php

/**
 * Model for handlers.
 */
class ThemeHouse_Objects_Model_Handler extends XenForo_Model
{
    const FETCH_HANDLER_TYPE = 0x01;

    /**
     * Gets handlers that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions
     *
     * @return array [handler id] => info.
     */
    public function getHandlers(array $conditions = array(), array $fetchOptions = array())
    {
        $whereClause = $this->prepareHandlerConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareHandlerFetchOptions($fetchOptions);

        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->fetchAllKeyed($this->limitQueryResults('
                SELECT handler.*
                    ' . $joinOptions['selectFields'] . '
                FROM xf_handler AS handler
                ' . $joinOptions['joinTables'] . '
                WHERE ' . $whereClause . '
            ', $limitOptions['limit'], $limitOptions['offset']
        ), 'handler_id');
    } /* END ThemeHouse_Objects_Model_Handler::getHandlers */

    /**
     * Gets the handler that matches the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array|false
     */
    public function getHandler(array $conditions = array(), array $fetchOptions = array())
    {
        $handlers = $this->getHandlers($conditions, $fetchOptions);

        return reset($handlers);
    } /* END ThemeHouse_Objects_Model_Handler::getHandler */

    /**
     * Gets a handler by ID.
     *
     * @param integer $handlerId
     * @param array $fetchOptions Options that affect what is fetched.
     *
     * @return array|false
     */
    public function getHandlerById($handlerId, array $fetchOptions = array())
    {
        $conditions = array('handler_id' => $handlerId);

        return $this->getHandler($conditions, $fetchOptions);
    } /* END ThemeHouse_Objects_Model_Handler::getHandlerById */

    /**
     * Gets the total number of a handler that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     *
     * @return integer
     */
    public function countHandlers(array $conditions = array())
    {
        $fetchOptions = array();

        $whereClause = $this->prepareHandlerConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareHandlerFetchOptions($fetchOptions);

        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        return $this->_getDb()->fetchOne('
            SELECT COUNT(*)
            FROM xf_handler AS handler
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereClause . '
        ');
    } /* END ThemeHouse_Objects_Model_Handler::countHandlers */

    /**
     * Gets all handlers titles.
     *
     * @return array [handler id] => title.
     */
    public static function getHandlerTitles()
    {
        $handlers = XenForo_Model::create(__CLASS__)->getHandlers();
        $titles = array();
        foreach ($handlers as $handlerId => $handler)
        {
            $titles[$handlerId] = $handler['content_type'];
        }
        return $titles;
    } /* END ThemeHouse_Objects_Model_Handler::getHandlerTitles */

    /**
     * Gets the default handler record.
     *
     * @return array
     */
    public function getDefaultHandler()
    {
        return array(
            'handler_id' => 0, 
            'title' => '', 
            'subtitle' => '', 
        );
    } /* END ThemeHouse_Objects_Model_Handler::getDefaultHandler */

    /**
     * Gets handlers that match the specified criteria.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions
     *
     * @return array [handler id] => info.
     */
    public function getHandlerTypeByHandlerId($handlerId)
    {
        $handlerType = $this->getHandlerById($handlerId, array('join' => self::FETCH_HANDLER_TYPE));

        return $handlerType;
    } /* END ThemeHouse_Objects_Model_Handler::getHandlerTypeByHandlerId */

    /**
     * Prepares a set of conditions to select handlers against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function prepareHandlerConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['handler_ids']) && !empty($conditions['handler_ids']))
        {
            $sqlConditions[] = 'handler.handler_id IN (' . $db->quote($conditions['handler_ids']) . ')';
        }

        if (isset($conditions['handler_id']) && $conditions['handler_id'])
        {
            $sqlConditions[] = 'handler.handler_id = ' . $db->quote($conditions['handler_id']);
        }

        $this->_prepareHandlerConditions($conditions, $fetchOptions, $sqlConditions);

        return $this->getConditionsForClause($sqlConditions);
    } /* END ThemeHouse_Objects_Model_Handler::prepareHandlerConditions */

    /**
     * Method designed to be overridden by child classes to add to set of conditions.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
     * @param array $sqlConditions List of conditions as SQL snippets. May be edited if criteria requires.
     */
    protected function _prepareHandlerConditions(array $conditions, array &$fetchOptions, array &$sqlConditions)
    {
        $db = $this->_getDb();

        if (isset($conditions['object_class_ids']) && !empty($conditions['object_class_ids']))
        {
            $sqlConditions[] = 'handler.object_class_id IN (' . $db->quote($conditions['object_class_id']) . ')';
        }

        if (isset($conditions['object_class_id']) && $conditions['object_class_id'])
        {
            $sqlConditions[] = 'handler.object_class_id = ' . $db->quote($conditions['object_class_id']);
        }
    } /* END ThemeHouse_Objects_Model_Handler::_prepareHandlerConditions */

    /**
     * Checks the 'join' key of the incoming array for the presence of the FETCH_x bitfields in this class
     * and returns SQL snippets to join the specified tables if required.
     *
     * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
     *
     * @return string containing selectFields, joinTables, orderClause keys.
     *          Example: selectFields = ', user.*, foo.title'; joinTables = ' INNER JOIN foo ON (foo.id = other.id) '; orderClause = 'ORDER BY x.y'
     */
    public function prepareHandlerFetchOptions(array &$fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        $this->_prepareHandlerFetchOptions($fetchOptions, $selectFields, $joinTables, $orderBy);

        return array(
            'selectFields' => $selectFields,
            'joinTables'   => $joinTables,
            'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
        );
    } /* END ThemeHouse_Objects_Model_Handler::prepareHandlerFetchOptions */

    /**
     * Method designed to be overridden by child classes to add to SQL snippets.
     *
     * @param array $fetchOptions containing a 'join' integer key built from this class's FETCH_x bitfields.
     * @param string $selectFields = ', user.*, foo.title'
     * @param string $joinTables = ' INNER JOIN foo ON (foo.id = other.id) '
     * @param string $orderBy = 'x.y ASC, x.z DESC'
     */
    protected function _prepareHandlerFetchOptions(array &$fetchOptions, &$selectFields, &$joinTables, &$orderBy)
    {
        if (isset($fetchOptions['join']))
        {
            if ($fetchOptions['join'] & self::FETCH_HANDLER_TYPE)
            {
                $selectFields .= ',
                    handler_type.*';
                $joinTables .= '
                    INNER JOIN xf_handler_type AS handler_type ON
                        (handler.handler_type_id = handler_type.handler_type_id)';
            }
        }
    } /* END ThemeHouse_Objects_Model_Handler::_prepareHandlerFetchOptions */
}