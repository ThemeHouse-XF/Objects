<?php

class ThemeHouse_Objects_Model_Class extends XenForo_Model
{
    const FETCH_ADDON = 0x01;

    /**
     * Gets an class by ID.
     *
     * @param string $classId
     *
     * @return array|false
     */
    public function getClassById($objectClassId)
    {
        return $this->_getDb()->fetchRow('
            SELECT *
            FROM xf_object_class
            WHERE object_class_id = ?
        ', $objectClassId);
    }

    /**
     * Gets classes that match the specified criteria.
     *
     * @param array $conditions
     * @param array $fetchOptions
     *
     * @return array [class id] => info
     */
    public function getClasses(array $conditions = array(), array $fetchOptions = array())
    {
        $whereClause = $this->prepareClassConditions($conditions, $fetchOptions);
        $joinOptions = $this->prepareClassFetchOptions($fetchOptions);

        return $this->fetchAllKeyed('
            SELECT object_class.*
            ' . $joinOptions['selectFields'] . '
            FROM xf_object_class AS object_class
            ' . $joinOptions['joinTables'] . '
            WHERE ' . $whereClause . '
            ' . $joinOptions['orderClause'] . '
        ', 'object_class_id');
    }

    /**
     * Gets all classes titles.
     *
     * @return array [class id] => title.
     */
    public static function getClassTitles()
    {
        $classes = XenForo_Model::create(__CLASS__)->getClasses();
        $titles = array();
        foreach ($classes as $classId => $class)
        {
            $titles[$classId] = $class['title'];
        }
        return $titles;
    } /* END ThemeHouse_Objects_Model_Class::getClassTitles */

    public function getAllClasses()
    {
        return $this->getClasses(array(), array('join' => self::FETCH_ADDON, 'orderBy' => 'addon_title'));
    }

    /**
     * Prepares a set of conditions to select classes against.
     *
     * @param array $conditions List of conditions.
     * @param array $fetchOptions The fetch options that have been provided. May be edited if criteria requires.
     *
     * @return string Criteria as SQL for where clause
     */
    public function prepareClassConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array();

        if (isset($conditions['object_class_ids']))
        {
            $sqlConditions[] = 'object_class.object_class_id IN (' . $db->quote($conditions['object_class_ids']) . ')';
        }

        if (isset($conditions['object_class_id']))
        {
            $sqlConditions[] = 'object_class.object_class_id = ' . $db->quote($conditions['object_class_id']);
        }

        if (isset($conditions['class_ids']))
        {
            $sqlConditions[] = 'object_class.class_id IN (' . $db->quote($conditions['class_ids']) . ')';
        }

        if (isset($conditions['class_id']))
        {
            $sqlConditions[] = 'object_class.class_id = ' . $db->quote($conditions['class_id']);
        }

        if (isset($conditions['addon_id']))
        {
            $sqlConditions[] = 'object_class.addon_id = ' . $db->quote($conditions['addon_id']);
        }

        if (!empty($conditions['active']))
        {
            $sqlConditions[] = 'addon.active = 1 OR object_class.addon_id = \'\' OR object_class.addon_id = \'XenForo\'';
            $this->addFetchOptionJoin($fetchOptions, self::FETCH_ADDON);
        }

        return $this->getConditionsForClause($sqlConditions);
    }

    /**
     * Prepares join-related fetch options.
     *
     * @param array $fetchOptions
     *
     * @return array Containing 'selectFields' and 'joinTables' keys.
     */
    public function prepareClassFetchOptions(array $fetchOptions)
    {
        $selectFields = '';
        $joinTables = '';
        $orderBy = '';

        if (!empty($fetchOptions['order']))
        {
            $orderBySecondary = '';

            switch ($fetchOptions['order'])
            {
                case 'addon_title':
                    $orderBy = 'addon.title';
                    $orderBySecondary = ', object_class.title ASC';
                    break;

                case 'title':
                default:
                    $orderBy = 'object_class.title';
            }
            if (!isset($fetchOptions['orderDirection']) || $fetchOptions['orderDirection'] == 'asc')
            {
                $orderBy .= ' ASC';
            }
            else
            {
                $orderBy .= ' DESC';
            }

            $orderBy .= $orderBySecondary;
        }

        if (!empty($fetchOptions['join']))
        {
            if ($fetchOptions['join'] & self::FETCH_ADDON)
            {
                $selectFields .= ',
                    addon.title AS addon_title, addon.active';
                $joinTables .= '
                    LEFT JOIN xf_addon AS addon ON
                        (object_class.addon_id = addon.addon_id)';
            }
        }

        return array(
                'selectFields' => $selectFields,
                'joinTables'   => $joinTables,
                'orderClause'  => ($orderBy ? "ORDER BY $orderBy" : '')
        );
    }

    /**
     * Fetches classes grouped by add-on
     *
     * @param array $conditions
     * @param array $fetchOptions
     * @param integer $classCount Reference: counts the total number of classes
     *
     * @return [add-on ID => [title, classes => class]]
     */
    public function getClassesByAddOns(array $conditions = array(), array $fetchOptions = array(), &$classCount = 0)
    {
        $this->addFetchOptionJoin($fetchOptions, self::FETCH_ADDON);

        $conditions['active'] = true;

        $classes = $this->getClasses($conditions, $fetchOptions);

        $addOns = array();
        foreach ($classes AS $class)
        {
            $addOns[$class['addon_id']][$class['class_id']] = $class;
        }

        $classCount = count($classes);

        return $addOns;
    }

    /**
     * Gets the XML representation of a class.
     *
     * @param array $class
     *
     * @return DOMDocument
     */
    public function getClassXml(array $class)
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $rootNode = $document->createElement('class');
        $this->_appendClassXml($rootNode, $class);
        if ($rootNode->hasChildNodes()) $document->appendChild($rootNode);

        return $document;
    }

    /**
     * Appends the add-on class XML to a given DOM object.
     *
     * @param DOMElement $rootNode Node to append all objects to
     * @param string $addOnId Add-on ID to be exported
     */
    public function appendClassesAddOnXml(DOMElement $rootNode, $addOnId)
    {
        $document = $rootNode->ownerDocument;

        $classes = $this->getClasses(array('addon_id' => $addOnId));
        foreach ($classes as $class)
        {
            $classNode = $document->createElement('class');
            $this->_appendClassXml($classNode, $class);
            $rootNode->appendChild($classNode);
        }
    }

    /**
     * @param DOMElement $rootNode
     * @param array $class
     */
    protected function _appendClassXml(DOMElement $rootNode, $class)
    {
        $document = $rootNode->ownerDocument;

        $rootNode->setAttribute('class_id', $class['class_id']);
        $rootNode->setAttribute('class_id_plural', $class['class_id_plural']);
        $rootNode->setAttribute('route_prefix', $class['route_prefix']);
        $rootNode->setAttribute('route_prefix_admin', $class['route_prefix_admin']);

        $titleNode = $document->createElement('title');
        $rootNode->appendChild($titleNode);
        $titleNode->appendChild(XenForo_Helper_DevelopmentXml::createDomCdataSection($document, $class['title']));

        $titleNode = $document->createElement('title_plural');
        $rootNode->appendChild($titleNode);
        $titleNode->appendChild(XenForo_Helper_DevelopmentXml::createDomCdataSection($document, $class['title_plural']));
        
        $titleNode = $document->createElement('title_full');
        $rootNode->appendChild($titleNode);
        $titleNode->appendChild(XenForo_Helper_DevelopmentXml::createDomCdataSection($document, $class['title_full']));
        
        $rootNode->setAttribute('table_name', $class['table_name']);
//        $rootNode->setAttribute('primary_key_id', $class['primary_key_id']);
    }

    /**
     * Imports a class XML file.
     *
     * @param SimpleXMLElement $document
     * @param string $fieldGroupId
     * @param integer $overwriteFieldId
     *
     * @return array List of cache rebuilders to run
     */
    public function importClassXml(SimpleXMLElement $document)
    {
        if ($document->getName() != 'class')
        {
            throw new XenForo_Exception(new XenForo_Phrase('provided_file_is_not_valid_class_xml'), true);
        }

        $classId = (string)$document['class_id'];
        if ($classId === '')
        {
            throw new XenForo_Exception(new XenForo_Phrase('provided_file_is_not_valid_class_xml'), true);
        }

        $addOnId = (string)$document['addon_id'];

        $overwriteClasses = $this->getClasses(array('class_id' => $classId, 'addon_id' => $addOnId));
        if (!empty($overwriteClasses))
        {
            $overwriteClass = reset($overwriteClasses);
        }

        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        $dw = XenForo_DataWriter::create('ThemeHouse_Objects_DataWriter_Class');
        if (isset($overwriteClass))
        {
            $dw->setExistingData($overwriteClass['object_class_id']);
        }
        else
        {
            $dw->set('class_id', $classId);
            $dw->set('addon_id', $addOnId);
        }

        $dw->bulkSet(array(
            'title' => XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->title),
            'class_id_plural' => $document['class_id_plural'],
            'title_plural' => XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->title_plural),
            'route_prefix' => $document['route_prefix'],
            'route_prefix_admin' => $document['route_prefix_admin'],
        	'title_full' => XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($document->title_full),
        	'table_name' => $document['table_name'],
        	'primary_key_id' => $document['primary_key_id'],
        ));

        $dw->save();

        XenForo_Db::commit($db);

        return array();
    }

    /**
     * Imports the add-on classes XML.
     *
     * @param SimpleXMLElement $xml XML object pointing to the root of the data
     * @param string $addOnId Add-on to import for
     * @param integer $maxExecution Maximum run time in seconds
     * @param integer $offset Number of objects to skip
     *
     * @return boolean|integer True on completion; false if the XML isn't correct; integer otherwise with new offset value
     */
    public function importClassesAddOnXml(SimpleXMLElement $xml, $addOnId, $maxExecution = 0, $offset = 0)
    {
        $db = $this->_getDb();

        XenForo_Db::beginTransaction($db);

        $startTime = microtime(true);

        $classes = XenForo_Helper_DevelopmentXml::fixPhpBug50670($xml->class);

        $current = 0;
        $restartOffset = false;
        foreach ($classes AS $class)
        {
            $current++;
            if ($current <= $offset)
            {
                continue;
            }

            $classId = (string)$class['class_id'];

            $class->addAttribute('addon_id', $addOnId);

            $this->importClassXml($class);

            if ($maxExecution && (microtime(true) - $startTime) > $maxExecution)
            {
                $restartOffset = $current;
                break;
            }
        }

        XenForo_Db::commit($db);

        return ($restartOffset ? $restartOffset : true);
    }
}