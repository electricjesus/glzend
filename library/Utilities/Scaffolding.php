<?php
/*
Copyright (c) 2011, Alex Oroshchuk
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.

    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.

    * Neither the name of Zend Technologies USA, Inc. nor the names of its
      contributors may be used to endorse or promote products derived from this
      software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/**
 * This Zend controller extension class allows you to quickly scaffold
 * and admin interface for an application, using Zend MVC core components.
 * The controllers you would like to scaffold must extend this one, and you will
 * automatically have create, update, delete and list actions.
 *
 * @author Alex Oroshchuk (oroshchuk@gmail.com)
 * @copyright 2011 Alex Oroshchuk
 * @version 0.8.1
 */
/* 
    @15 - June 2011 | changed class name from Zend_Controller_Scaffolding -> Utilities_Scaffolding - electricjesus
*/ 
class Utilities_Scaffolding extends Zend_Controller_Action
{

    /**
     * Controller actions used as CRUD operations.
     */
    const ACTION_INDEX  = 'index';
    const ACTION_LIST   = 'list';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * Identifier used in view generation.
     */
    const ID_TOKEN      = 'zs';

    /**
     * Create form button definitions.
     */
    const BUTTON_SAVE       = 'save';
    const BUTTON_SAVEEDIT   = 'saveedit';
    const BUTTON_SAVECREATE = 'savecreate';

    /**
     * Create form button default labels.
     */
    protected $buttonLabels    = array(
        self::BUTTON_SAVE       => 'Save',
        self::BUTTON_SAVEEDIT   => 'Save and continue editing',
        self::BUTTON_SAVECREATE => 'Save and create new one'
    );

    /**
     * Messages displayed upon record creation, update or deletion.
     */
    protected $messageTemplates = array(
        self::ACTION_CREATE => array(
            'ok'    => 'New %s has been created.',
            'error' => 'Failed to create new %s.'
            ),
        self::ACTION_UPDATE => array(
            'ok'    => 'The %s has been updated.',
            'error' => 'Failed to update %s.'
            ),
        self::ACTION_DELETE => array(
            'ok'    => 'The %s has been deleted.',
            'error' => 'Failed to delete %s.'
            )
    );

    /**
     * Data providing class.
     * @var Zend_Db_Table_Abstract|Zend_Db_Table_Select|Zend_Db_Select
     */
    protected $scaffSelectCriteria;

    /**
     * Default scaffolding options.
     * @var Array
     */
    private $scaffOptions = array(
        'pkEditable'        => false,
        'viewFolder'        => 'scaffolding',
        'entityTitle'       => 'entity',
        'readonly'          => false,
        'disabledActions'   => array(),
        'createButtons'     => array(
            self::BUTTON_SAVE,
            self::BUTTON_SAVEEDIT,
            self::BUTTON_SAVECREATE
        ),
        'csrfProtected'     => true
    );

    /**
     * Scaffolding field definitions.
     * @var Array
     */
    private $scaffFields;

    /**
     * Data providing class.
     * @var Zend_Db_Table_Abstract|Zend_Db_Table_Select|Zend_Db_Select
     */
    private $scaffDb;

    /**
     * Cached table metadata.
     * @var Array
     */
    private $scaffMeta;

    /**
     * General controller initialization.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Initializes scaffolding.
     *
     * @param Zend_Db_Table_Abstract|Zend_Db_Select $dataProvider respective model instance
     * @param array $fields field definitions
     * @param Zend_Config|Array $options
     */
    protected function initScaffolding($dataProvider, $fields = array(), $options = null)
    {
        if (!($dataProvider instanceof Zend_Db_Table_Abstract
                || $dataProvider instanceof Zend_Db_Select || $dataProvider instanceof Zend_Db_Table_Select)) {
            throw new Zend_Controller_Exception(
                    'Scaffolding initialization requires'
                    . ' an instance of Zend_Db_Table_Abstract,'
                    . ' Zend_Db_Table_Select or Zend_Db_Select');
        }

        // If readonly restrict all other actions except for index and list
        // @todo: reverse check - enable readonly if all actions disabled
        if (isset($options['readonly']) && $options['readonly']) {
            $this->scaffOptions['disabledActions'] =
                array(self::ACTION_CREATE, self::ACTION_DELETE, self::ACTION_UPDATE);
        }

        $this->scaffDb     = $dataProvider;
        $this->scaffFields = $fields;
        if (is_array($options)) {
            $this->scaffOptions = array_merge($this->scaffOptions, $options);
        }

        // Do not override view script path if the action requested is not
        // one of the standard scaffolding actions
        $action         = $this->getRequest()->getActionName();
        $scaffActions   = array(self::ACTION_LIST, self::ACTION_INDEX,
                              self::ACTION_CREATE, self::ACTION_UPDATE,
                              self::ACTION_DELETE);
        $indexActionScript = null;
        if (isset($this->scaffOptions['useIndexAction']) && $this->scaffOptions['useIndexAction']) {
            $scaffActions[]     = $action;
            $indexActionScript  = 'index';
        }

        if (in_array($action, $scaffActions)) {
            $this->getHelper('ViewRenderer')
                 ->setViewScriptPathSpec(
                        sprintf('%s/' . ($indexActionScript ? $indexActionScript : ':action') . '.:suffix', $this->scaffOptions['viewFolder']));
        }

        $this->view->module     = $this->getRequest()->getModuleName();
        $this->view->controller = $this->getRequest()->getControllerName();
        $this->view->messages   = $this->_helper->getHelper('FlashMessenger')->getMessages();
    }

    /**
     * Entity update handler.
     */
    public function updateAction()
    {
        if (in_array(self::ACTION_UPDATE, $this->scaffOptions['disabledActions'])) {
            throw new Zend_Controller_Exception('This action is disabled.');
        }

        $info = $this->_getMetadata();

        if (count($info['primary']) == 0) {
            throw new Zend_Controller_Exception('The model you provided does not have a primary key, scaffolding is impossible.');
        } else {
            // Support compound keys
            $primaryKey = array();
            $params = $this->_getAllParams();
            foreach($params AS $k => $v) {
                if (in_array($k, $info['primary'])) {
                    $primaryKey["$k = ?"] = $v;
                }
            }
            $entity = $this->scaffDb->fetchAll($primaryKey);
            if ($entity->count()) {
                $entity = $entity->current()->toArray();
            } else {
                throw new Zend_Controller_Exception('Invalid request.');
            }

            $form = $this->_initEditForm($entity);
            $populate = true;

            if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
                $populate = false;
                $formValues = $form->getValues();
                $pkValue = $formValues[array_shift($info['primary'])];

                list($values, $where, $relData) = $this->_getDbValuesUpdate($entity, $formValues);

                // Save common submitted fields
                if (!is_null($values) && !is_null($where)) {
                    if ($this->_beforeUpdate($form, $values)) {

                        try {
                            Zend_Db_Table::getDefaultAdapter()->beginTransaction();

                            $this->scaffDb->update($values, $where);
                            // Save many-to-many field to the corresponding table
                            if (count($relData)) {
                                foreach ($relData as $m2mData) {
                                    $m2mTable   = $m2mData[0];
                                    $m2mValues  = is_array($m2mData[1]) ? $m2mData[1] : array();

                                    $m2mInfo    = $m2mTable->info();
                                    $tableClass = get_class($this->scaffDb);
                                    foreach ($m2mInfo['referenceMap'] as $rule => $ruleDetails) {
                                        if ($ruleDetails['refTableClass'] == $tableClass) {
                                            $selfRef = $ruleDetails['columns'];
                                        } else {
                                            $relatedRef = $ruleDetails['columns'];
                                        }
                                    }

                                    $m2mTable->delete("$selfRef = $pkValue");

                                    foreach ($m2mValues as $v) {
                                        $m2mTable->insert(array($selfRef => $pkValue, $relatedRef => $v));
                                    }
                                }
                            }

                            Zend_Db_Table::getDefaultAdapter()->commit();
                            $this->_helper->FlashMessenger(sprintf($this->messageTemplates[self::ACTION_UPDATE]['ok'], $this->scaffOptions['entityTitle']));

                            if ($this->_afterUpdate($form)) {
                                $this->_redirect($this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/index');
                            }
                        } catch (Zend_Db_Exception $e) {
                            Zend_Db_Table::getDefaultAdapter()->rollBack();
                            $this->view->messages[] = sprintf($this->messageTemplates[self::ACTION_UPDATE]['error'], $this->scaffOptions['entityTitle']);
                        }
                    }
                }
            }

            if ($populate === true) {
                // Load common field values
                foreach ($entity as $field => $value)
                    // Apply field modifier if any
                    if (isset($this->scaffFields[$field]['loadModifier'])) {
                        if (function_exists($this->scaffFields[$field]['loadModifier'])) {
                            $entity[$field] = call_user_func($this->scaffFields[$field]['loadModifier'], $value);
                        } else {
                            $entity[$field] = $this->scaffFields[$field]['loadModifier'];
                        }
                    }

                // Load many-to-many field values
                foreach ($this->scaffFields as $field => $fieldDetails) {
                    if (isset($fieldDetails['dependentTable'])) {
                        $m2mTable = $fieldDetails['dependentTable'];
                        $m2mInfo = $m2mTable->info();

                        $tableClass = get_class($this->scaffDb);
                        foreach ($m2mInfo['referenceMap'] as $rule => $ruleDetails) {
                            if ($ruleDetails['refTableClass'] == $tableClass) {
                                $selfRef = $ruleDetails['columns'];
                            } else {
                                $relatedRef = $ruleDetails['columns'];
                            }
                        }

                        $m2mValues = $m2mTable->select()
                                              ->from($m2mTable, $relatedRef)
                                              ->where("$selfRef = ?", $primaryKey)
                                              ->query(Zend_Db::FETCH_ASSOC)->fetchAll();

                        $multiOptions = array();
                        foreach ($m2mValues as $_value) {
                            $multiOptions[] = $_value[$relatedRef];
                        }

                        $entity[$field] = $multiOptions;
                    }
                }

                $form->setDefaults($entity);
            }

            $this->view->form           = $form;
            $this->view->entityTitle    = $this->scaffOptions['entityTitle'];
            if (isset($this->scaffOptions['editLayout'])) {
                $this->_helper->layout->setLayout($this->scaffOptions['editLayout']);
            }
        }
    }

    /**
     * Create entity handler.
     */
    public function createAction()
    {
        if (in_array(self::ACTION_CREATE, $this->scaffOptions['disabledActions'])) {
            throw new Zend_Controller_Exception('This action is disabled.');
        }

        $info = $this->_getMetadata();

        if (count($info['primary']) == 0) {
            throw new Zend_Controller_Exception('The model you provided does not have a primary key, scaffolding is impossible.');
        }

        $form = $this->_initEditForm();

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            list($values, $relData) = $this->_getDbValuesInsert($form->getValues());

            if ($this->_beforeCreate($form, $values)) {

                try {
                    Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                    $insertId = $this->scaffDb->insert($values);
                    // Save many-to-many field to the corresponding table
                    if (count($relData)) {
                        foreach ($relData as $m2mData) {
                            $m2mTable   = $m2mData[0];
                            $m2mValues  = $m2mData[1];

                            if (count($m2mValues)) {
                                $m2mInfo    = $m2mTable->info();
                                $tableClass = get_class($this->scaffDb);
                                foreach ($m2mInfo['referenceMap'] as $rule => $ruleDetails) {
                                    if ($ruleDetails['refTableClass'] == $tableClass) {
                                        $selfRef = $ruleDetails['columns'];
                                    } else {
                                        $relatedRef = $ruleDetails['columns'];
                                    }
                                }

                                foreach ($m2mValues as $v) {
                                    $m2mTable->insert(array($selfRef => $insertId, $relatedRef => $v));
                                }
                            }
                        }
                    }

                    Zend_Db_Table::getDefaultAdapter()->commit();

                    $this->_helper->FlashMessenger(sprintf($this->messageTemplates[self::ACTION_CREATE]['ok'], $this->scaffOptions['entityTitle']));

                    if ($this->_afterCreate($form, $insertId)) {
                        if (isset($_POST[self::BUTTON_SAVE])) {
                            $redirect = $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/index';
                        } elseif (isset($_POST[self::BUTTON_SAVEEDIT])) {
                            $redirect = $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/update/id/' . $insertId;
                        } elseif (isset($_POST[self::BUTTON_SAVECREATE])) {
                            $redirect = $this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/create';
                        }

                        $this->_redirect($redirect);
                    }
                }
                catch (Zend_Db_Exception $e) {
                    Zend_Db_Table::getDefaultAdapter()->rollBack();
                    $this->view->messages[] = sprintf($this->messageTemplates[self::ACTION_CREATE]['error'], $this->scaffOptions['entityTitle']);
                }
            }
        }

        $this->view->form           = $form;
        $this->view->entityTitle    = $this->scaffOptions['entityTitle'];
        if (isset($this->scaffOptions['editLayout'])) {
            $this->_helper->layout->setLayout($this->scaffOptions['editLayout']);
        }
    }

    /**
     * Display the list of entries, as well as optional elements
     * like paginator, search form and sortable headers as specified
     * in field definition.
     */
    public function indexAction()
    {
        $fields         = array();
        $fieldAliases   = array();
        $searchFields   = array();
        $searchForm     = null;
        $searchActive   = false;

        if (in_array(self::ACTION_INDEX, $this->scaffOptions['disabledActions'])) {
            throw new Zend_Controller_Exception('This action is disabled.');
        }

        if ($this->scaffDb instanceof Zend_Db_Table_Abstract
                || $this->scaffDb instanceof Zend_Db_Table_Select) {
            if ($this->scaffDb instanceof Zend_Db_Table_Abstract) {
                $select = $this->scaffDb->select();
            } else {
                $select = $this->scaffDb;
            }

            $tableInfo  = $this->_getMetadata();
            $pks        = $tableInfo['primary'];
            $joins      = array();

            // Fetch fields using specified order
            foreach ($tableInfo['cols'] as $columnName) {
                $skip = isset($this->scaffFields[$columnName]['skip']) ?
                        $this->scaffFields[$columnName]['skip'] : false;
                if (!in_array($columnName, $pks) && $skip) {
                    if ($skip === true || strtolower($skip) == 'list') {
                        continue;
                    }
                }

                $order = isset($this->scaffFields[$columnName]['order']) ?
                         $this->scaffFields[$columnName]['order'] : null;
                if ($order) {
                    if (!isset($fields[$order])) {
                        $fields[$order] = $columnName;
                    } else {
                        $fields[] = $columnName;
                    }
                } else {
                    $fields[] = $columnName;
                }

                // Check if column is a foreign key and its list value must be taken from a parent table
                // @todo: use getReference?
                if (isset($this->scaffFields[$columnName]['asText'])
                        && $this->scaffFields[$columnName]['asText']) {

                    if (count($tableInfo['referenceMap']) > 0) {
                        $match = false;
                        $refColumnPartner = false;
                        foreach ($tableInfo['referenceMap'] AS $rule => $ruleDetails) {
                            // Try to find the column in list of foreign key columns.
                            if (is_array($ruleDetails['columns']) && ($colId = array_search($columnName, $ruleDetails['columns'])) !== false) {
                                $refColumnPartner = $ruleDetails['refColumns'][$colId];
                                $match = true;
                            } elseif (is_string($ruleDetails['columns']) && $columnName == $ruleDetails['columns']) {
                                $refColumnPartner = is_array($ruleDetails['refColumns']) ?
                                                    array_shift($ruleDetails['refColumns']) : $ruleDetails['refColumns'];
                                $match = true;
                            }

                            if ($match && $refColumnPartner !== false) {
                                $depModel = new $ruleDetails['refTableClass']();

                                if (isset($depModel->titleField)) {
                                    $namingFieldKey = $depModel->titleField;

                                    // Join with parent table
                                    $depTableMetadata = $depModel->info();

                                    $joins[] = array ($depTableMetadata['name'],
                                                "{$tableInfo['name']}.$columnName = {$depTableMetadata['name']}.$refColumnPartner",
                                                null);

                                    $fid = array_search($columnName, $fields);
                                    $fields[$fid] = "{$depTableMetadata['name']}.$namingFieldKey AS $columnName";
                                    $fieldAliases["{$depTableMetadata['name']}.$namingFieldKey AS $columnName"] = $columnName;
                                }
                                else {
                                    // @todo: should an exception be thrown?
                                }

                                break;
                            }
                        }
                    }
                }

                // Prepare search form element
                if (isset($this->scaffFields[$columnName]['searchable'])) {
                    $searchFields[] = $columnName;
                }
            }

            ksort($fields);
            if ($this->scaffDb instanceof Zend_Db_Table_Abstract) {
                $select->from($this->scaffDb, $fields);
                if (count($joins)) {
                    // Workaround required by Zend_Db_Table_Select concept
                    $select->setIntegrityCheck(false);

                    foreach ($joins as $joinInfo) {
                        $select->joinLeft($joinInfo[0], $joinInfo[1], $joinInfo[2]);
                    }
                }
            }
            else {
                $select->from($this->scaffDb->getTable(), $fields);
            }
        } elseif ($this->scaffDb instanceof Zend_Db_Select) {
            $select = $this->scaffDb;
            $pks    = array();
            $fields = array_keys($this->scaffFields);

            // Prepare search form element
            foreach ($fields as $field) {
                if (isset($this->scaffFields[$field]['searchable'])) {
                    $searchFields[] = $field;
                }
            }
        }

        /**
         * Apply search filter, storing search criteria in session.
         */
        $searchActive = false;
        if (count($searchFields)) {
            if (isset($tableInfo['name'])) {
                $nsName = self::ID_TOKEN . '_' . $tableInfo['name'];
            } else {
                $nsName = self::ID_TOKEN . '_' . join('_', $searchFields);
            }

            $searchParams   = new Zend_Session_Namespace($nsName);
            $searchForm     = $this->_initSearchForm($searchFields);

            if ($this->getRequest()->isPost() && $searchForm->isValid($this->getRequest()->getPost())) {
                if (isset($_POST['reset'])) {
                    $filterFields = array();
                } else {
                    $filterFields = $searchForm->getValues();
                }
                $searchParams->search   = $filterFields;
            } else {
                $filterFields = isset($searchParams->search) ? $searchParams->search : array();
            }
            $searchForm->populate($filterFields);

            foreach ($filterFields as $field => $value) {
                // Search by synthetic field
                if (!isset($this->scaffFields[$field]) && !isset($tableInfo['metadata'][$field])) {
                    if (strpos($field, 'searchempty') !== false && $value) {
                        $field = str_replace('searchempty', '', $field);
                        $select->where("($field IS NULL OR $field = '')");
                        $searchActive = true;
                    }
                } elseif ($value || is_numeric($value)) {
                    // Search date fields
                    if (strpos($field, self::ID_TOKEN . '_from')) {
                        $select->where(str_replace('_' . self::ID_TOKEN . '_from', '', $field) . " >= ?", $value);
                    } elseif (strpos($field, self::ID_TOKEN . '_to')) {
                        $select->where(str_replace('_' . self::ID_TOKEN . '_to', '', $field) . " <= ?", $value);
                    } else {
                        // Search all other fields
                        if (isset($tableInfo['metadata'])) {
                            $dataType = strtolower($tableInfo['metadata'][$field]['DATA_TYPE']);
                            $fieldType = null;
                        } else {
                            $dataType = '';
                            if ($this->scaffFields[$field]['type'] == 'text')
                                $fieldType = 'text';
                        }

                        if (in_array($dataType, array('char','varchar','text')) || $fieldType == 'text') {
                            $select->where("$field LIKE ?", $value);
                        } else {
                            $select->where("$field = ?", $value);
                        }
                    }
                    $searchActive = true;
                }
            }
        }

        // Save criteria
        $this->scaffSelectCriteria = clone $select;

        /**
         * Handle sorting by modifying SQL and building header sorting links.
         */
        $sortField  = $this->_getParam('orderby');
        $sortMode   = $this->_getParam('mode') == 'desc' ? 'desc' : 'asc';

        if ($sortField) {
            $select->order("$sortField $sortMode");
        }

        $headers = array();

        foreach ($fields as $columnName) {
            if (isset($fieldAliases[$columnName])) {
                $columnName = $fieldAliases[$columnName];
            }

            $name = $this->_getColumnTitle($columnName);

            $skip = isset($this->scaffFields[$columnName]['skip']) ?
                    $this->scaffFields[$columnName]['skip'] : false;
            if ($skip) {
                if ($skip === true || strtolower($skip) == 'list') {
                    continue;
                }
            }

            // Generate sorting link
            if (isset($this->scaffFields[$columnName]['sortable'])
                    && $this->scaffFields[$columnName]['sortable'] == true) {
                // Does a default sorting exist?
                if (!$sortField && isset($this->scaffFields[$columnName]['sortBy'])) {
                    $sortField  = $columnName;
                    $sortMode   = $this->scaffFields[$columnName]['sortBy'] == 'desc' ? 'desc' : 'asc';
                    $select->order("$sortField $sortMode");
                }

                $currentMode = ($sortField == $columnName ? $sortMode : '');

                if ($currentMode == 'asc') {
                    $counterOrder   = 'desc';
                    $class          = self::ID_TOKEN . '-sort-desc';
                } elseif ($currentMode == 'desc') {
                    $counterOrder   = 'asc';
                    $class          = self::ID_TOKEN . '-sort-asc';
                } else {
                    $counterOrder   = 'asc';
                    $class          = '';
                }

                $sortParams = array(
                    'orderby'   => $columnName,
                    'mode'      => $counterOrder
                    );

                $href = $this->view->url($sortParams, 'default');
                $headers[$columnName] = "<a class=\"" . self::ID_TOKEN . "-sort-link $class\" href=\"$href\">$name</a>";
            } else {
                $headers[$columnName] = $name;
            }
        }

        // Enable paginator if needed
        if (isset($this->scaffOptions['pagination'])) {
            $pageNumber = $this->_getParam('page');
            $paginator = Zend_Paginator::factory($select);

            $paginator->setCurrentPageNumber($pageNumber);
            $itemPerPage = isset($this->scaffOptions['pagination']['itemsPerPage']) ?
                            $this->scaffOptions['pagination']['itemsPerPage'] : 10;
            $paginator->setItemCountPerPage($itemPerPage);

            $items = $paginator->getItemsByPage($pageNumber);

            if ($items instanceof Zend_Db_Table_Rowset) {
                $items = $items->toArray();
            } elseif ($items instanceof ArrayIterator) {
                $items = $items->getArrayCopy();
            }

            $entries = $this->_prepareDbRecordsList($items);
            $this->view->paginator = $paginator;
            $this->view->pageNumber = $pageNumber;
        } else {
            $entries = $this->_prepareDbRecordsList($select->query()->fetchAll());
        }

        $this->view->headers        = $headers;
        $this->view->entries        = $entries;
        $this->view->entityTitle    = $this->scaffOptions['entityTitle'];
        $this->view->readonly       = $this->scaffOptions['readonly'];
        $this->view->searchActive   = $searchActive;
        $this->view->searchForm     = $searchForm;
        $this->view->primaryKey     = $pks;

        $this->view->canCreate      = !in_array(self::ACTION_CREATE, $this->scaffOptions['disabledActions']);
        $this->view->canUpdate      = !in_array(self::ACTION_UPDATE, $this->scaffOptions['disabledActions']);
        $this->view->canDelete      = !in_array(self::ACTION_DELETE, $this->scaffOptions['disabledActions']);
    }

    /**
     * Alias of index action.
     */
    public function listAction()
    {
        if (in_array(self::ACTION_LIST, $this->scaffOptions['disabledActions'])) {
            throw new Zend_Controller_Exception('This action is disabled.');
        }

        $this->_forward('index');
    }

    /**
     * Entity deletion handler.
     */
    public function deleteAction()
    {

        if (in_array(self::ACTION_DELETE, $this->scaffOptions['disabledActions'])) {
            throw new Zend_Controller_Exception('This action is disabled.');
        }

        $params = $this->_getAllParams();
        $info = $this->_getMetadata();

        if (count($info['primary']) == 0) {
            throw new Zend_Controller_Exception('The model you provided does not have a primary key, scaffolding is impossible!');
        } else {
            // Compound key support
            $primaryKey = array();
            foreach ($params AS $k => $v) {
                if (in_array($k, $info['primary'])) {
                    $primaryKey["$k = ?"] = $v;
                }
            }

            try {
                $row = $this->scaffDb->fetchAll($primaryKey);
                if ($row->count()) {
                    $row = $row->current();
                } else {
                    throw new Zend_Controller_Exception('Invalid request.');
                }

                $originalRow = clone $row;

                if ($this->_beforeDelete($originalRow)) {
                    $row->delete();
                    $this->_helper->FlashMessenger(sprintf($this->messageTemplates[self::ACTION_DELETE]['ok'], $this->scaffOptions['entityTitle']));

                    if ($this->_afterDelete($originalRow)) {
                        $this->_redirect($this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/index');
                    }
                }
            } catch (Zend_Db_Exception $e) {
                $this->_helper->FlashMessenger(sprintf($this->messageTemplates[self::ACTION_DELETE]['ok'], $this->scaffOptions['entityTitle']));
                $this->_redirect($this->getRequest()->getModuleName() . '/' . $this->getRequest()->getControllerName() . '/index');
            }
        }
    }

    /**
     * Generates the create/update form based on table metadata
     * and field definitions provided at initialization.
     *
     * @param array $entityData currently editable entity data
     * @return Zend_Form
     */
    private function _initEditForm(array $entityData = array())
    {
        $info       = $this->_getMetadata();
        $metadata   = $info['metadata'];
        $tableClass = get_class($this->scaffDb);
        $action     = $this->getRequest()->getActionName();
        $form       = array();
        $rteFields  = $datePickerFields = array();

        foreach ($metadata as $columnName => $columnDetails) {

            // Primary key is hidden by default
            if($this->scaffOptions['pkEditable'] == false && in_array($columnName, $info['primary'])) {
                $form['elements'][$columnName] = array(
                    'hidden', array(
                        'value' => 0,
                    )
                );
                continue;
            }

            // Skip the field?
            $skip = isset($this->scaffFields[$columnName]['skip']) ?
                    $this->scaffFields[$columnName]['skip'] : false;
            if ($skip) {
                if ($skip === true || strtolower($skip) == 'edit') {
                    continue;
                }
            }

            // Is the field mandatory?
            if (isset($this->scaffFields[$columnName]['required'])) {
                if (is_string($this->scaffFields[$columnName]['required'])) {
                    if ($this->scaffFields[$columnName]['required'] == 'onCreate' && $action == self::ACTION_UPDATE) {
                        $required = false;
                    }
                } else {
                    $required = $this->scaffFields[$columnName]['required'];
                }
            } else {
                $required = $columnDetails['NULLABLE'] == 1 ? false : true;
            }

            // Does it have a default value?
            if (!is_null($columnDetails['DEFAULT'])) {
                $defaultValue = $columnDetails['DEFAULT'];
            } else {
                $defaultValue = '';
            }

            // Determine if the column is a foreign key and build necessary select/multicheckbox field.
            // @todo: use getReference?
            if (count($info['referenceMap']) > 0) {
                $match = false;
                $refColumnPartner = false;
                foreach ($info['referenceMap'] AS $rule => $ruleDetails) {
                    // Try to find the column in list of foreign key columns.
                    if (is_array($ruleDetails['columns']) && ($colId = array_search($columnName, $ruleDetails['columns'])) !== false) {
                        $refColumnPartner = $ruleDetails['refColumns'][$colId];
                        $match = true;
                    } elseif (is_string($ruleDetails['columns']) && $columnName == $ruleDetails['columns']) {
                        $refColumnPartner = is_array($ruleDetails['refColumns']) ?
                                            array_shift($ruleDetails['refColumns']) : $ruleDetails['refColumns'];
                        $match = true;
                    }

                    if ($match === true && $refColumnPartner !== false) {
                        $options = array();
                        // Is value required?
                        if ($columnDetails['NULLABLE'] == 1 || !$required) {
                            $options[""] = "";
                        }

                        $depModel = new $ruleDetails['refTableClass']();
                        $namingFieldKey = $refColumnPartner;

                        if (isset($depModel->titleField)) {
                            $namingFieldKey = $depModel->titleField;
                        }

                        foreach ($depModel->fetchAll()->toArray() AS $k => $v) {
                            $key = $v[$refColumnPartner]; // obtain value of partner column
                            if (!isset($options[$key])) {
                                $options[$key] = $v[$namingFieldKey];
                            }
                        }

                        $form['elements'][$columnName] = array(
                            'select', array(
                                'multiOptions'  => $options,
                                'label'         => $this->_getColumnTitle($columnName),
                                'description'   => $this->_getColumnDescription($columnName),
                                'required'      => $required,
                                'value'         => $defaultValue,
                            )
                        );
                        break;
                    }
                }

                // Foreign key field has been generated, go to next field
                if ($match === true) {
                    continue;
                }
            }

            $elementOptions = array(
                'label'         => $this->_getColumnTitle($columnName),
                'description'   => $this->_getColumnDescription($columnName),
                'required'      => $required,
                'value'         => $defaultValue,
                'validators'    => isset($this->scaffFields[$columnName]['validators'])
                                        ? $this->_prepareValidators($columnName, $this->scaffFields[$columnName]['validators'], $entityData)
                                        : array(),
                'filters'       => isset($this->scaffFields[$columnName]['filters'])
                                        ? $this->scaffFields[$columnName]['filters'] : array(),
            );

            // Build enum column as select or multicheckbox.
            if (preg_match('/^enum/i', $columnDetails['DATA_TYPE'])) {
                $enumDefinition = $columnDetails['DATA_TYPE'];
                $dataType       = 'enum';
            } elseif (isset($this->scaffFields[$columnName]['options'])) {
                // Pseudo data type
                $dataType = 'options';
            } else {
                $dataType = strtolower($columnDetails['DATA_TYPE']);
            }

            $textFieldOptions   = array();
            $textFieldType      = null;

            if (isset($this->scaffFields[$columnName]['type'])) {
                switch ($this->scaffFields[$columnName]['type']) {
                    case 'textarea': case 'richtextarea':
                        $textFieldType  = 'textarea';
                        $rteFields[]    = $columnName;
                        break;

                    case 'text':
                        $textFieldType = 'text';
                        break;

                    case 'datepicker':
                        $datePickerFields[] = $columnName;
                        break;
                }

                if ($textFieldType == 'text') {
                    if (isset($this->scaffFields[$columnName]['size'])) {
                        $textFieldOptions['size'] = $this->scaffFields[$columnName]['size'];
                    }
                    if (isset($this->scaffFields[$columnName]['maxlength'])) {
                        $textFieldOptions['maxlength'] = $this->scaffFields[$columnName]['maxlength'];
                    } elseif (isset($metadata[$columnName]['LENGTH'])) {
                        $textFieldOptions['maxlength'] = $metadata[$columnName]['LENGTH'];
                    }
                } elseif ($textFieldType == 'textarea') {
                    if (isset($this->scaffFields[$columnName]['cols'])) {
                        $textFieldOptions['cols'] = $this->scaffFields[$columnName]['cols'];
                    }
                    if (isset($this->scaffFields[$columnName]['rows'])) {
                        $textFieldOptions['rows'] = $this->scaffFields[$columnName]['rows'];
                    }
                }
            }

            switch ($dataType) {
                // Build radio/select element from enum/options
                case 'enum': case 'options':
                    // Try to parse enum definition
                    if (isset($enumDefinition)) {
                        preg_match_all('/\'(.*?)\'/', $enumDefinition, $matches);

                        $options = array();
                        foreach ($matches[1] as $match) {
                            $options[$match] = ucfirst($match);
                        }
                    } else {
                        // Not enum - use options provided
                        $options = $this->scaffFields[$columnName]['options'];
                    }

                    if (isset($this->scaffFields[$columnName]['type']) && $this->scaffFields[$columnName]['type'] == 'radio') {
                        $elementType = 'radio';
                    } else {
                        $elementType = 'select';
                    }

                    $form['elements'][$columnName] = array(
                        $elementType,
                        array_merge(array('multiOptions'  => $options), $elementOptions)
                    );

                    break;

                // Generate fields for numerics.
                case 'tinyint':
                case 'bool':
                case 'smallint':
                case 'int':
                case 'integer':
                case 'mediumint':
                case 'bigint':

                    if (isset($this->scaffFields[$columnName]['type'])
                            && $this->scaffFields[$columnName]['type'] == 'checkbox') {
                        $form['elements'][$columnName] = array(
                            'checkbox',
                            $elementOptions
                        );
                    } else {
                        $form['elements'][$columnName] = array(
                            'text',
                            array_merge(array('size' => 10), $elementOptions)
                        );
                    }
                    break;

                case 'decimal':
                case 'float':
                case 'double':
                    $form['elements'][$columnName] = array(
                        'text',
                        $elementOptions
                    );
                    break;

                // Generate single-line input or multiline input for string fields.
                case 'char':
                case 'varchar':
                case 'smalltext':
                    $form['elements'][$columnName] = array(
                        $textFieldType ? $textFieldType : 'text',
                        array_merge($elementOptions, $textFieldOptions)
                    );
                    break;

                case 'text':
                case 'mediumtext':
                case 'longtext':
                    $form['elements'][$columnName] = array(
                        $textFieldType ? $textFieldType : 'textarea',
                        array_merge($elementOptions, $textFieldOptions)
                    );
                    break;

                // Date/time fields.
                case 'date':
                case 'time':
                case 'datetime':
                case 'timestamp':
                    $form['elements'][$columnName] = array(
                        'text',
                        $elementOptions
                    );
                    break;

                default:
                    throw new Zend_Controller_Exception("Unsupported data type '$dataType' encountered, scaffolding is not possible.");
                    break;
            }
        }

        // Look for additional field definitions (not from model).
        foreach ($this->scaffFields as $columnName => $columnDetails) {
            // Determine if the column is a many-to-may relationship item.
            if (isset($columnDetails['dependentTable'])) {

                $dependentTable = $columnDetails['dependentTable'];
                if (!$dependentTable instanceof Zend_Db_Table_Abstract) {
                    throw new Zend_Controller_Exception('Zend_Controller_Scaffolding requires a Zend_Db_Table_Abstract model as "dependentTable" field option.');
                }

                $dtInfo = $dependentTable->info();

                foreach($dtInfo['referenceMap'] AS $rule => $ruleDetails) {
                    /**
                     * Try to find the column in list of foreign key columns
                     * that IS NOT referencing current table but another
                     * independent table, and fetch possible values from it.
                     */
                    if (is_string($ruleDetails['columns']) && $tableClass != $ruleDetails['refTableClass']) {
                        $optionsTable = new $ruleDetails['refTableClass'];

                        // Auto-detect PK based on metadata
                        if (!isset($ruleDetails['refColumns'])) {
                            $optionsTableInfo = $optionsTable->info();
                            $ruleDetails['refColumns'] = array_shift($optionsTableInfo['primary']);
                        }

                        // @todo: one column assumed
                        $namingFieldKey = $ruleDetails['refColumns'];
                        if (isset($optionsTable->titleField)) {
                            $namingFieldKey = $optionsTable->titleField;
                        }

                        // Value required?
                        $required = isset($columnDetails['required']) && $columnDetails['required'] ? true : false;

                        $options = array();
                        foreach($optionsTable->fetchAll()->toArray() AS $k => $v) {
                            $key = $v[$ruleDetails['refColumns']];
                            if (!isset($options[$key])) {
                                $options[$key] = $v[$namingFieldKey];
                            }
                        }

                        if (isset($columnDetails['type']) && $columnDetails['type'] == 'multicheckbox') {
                            $elementType = 'MultiCheckbox';
                        } else {
                            $elementType = 'Multiselect';
                        }

                        $form['elements'][$columnName] = array(
                            $elementType, array(
                                'multiOptions' => $options,
                                'label' => $this->_getColumnTitle($columnName),
                                'description'   => $this->_getColumnDescription($columnName),
                                'required'  => $required,
                                'validators'    => isset($this->scaffFields[$columnName]['validators']) ?
                                                   $this->_prepareValidators($columnName, $this->scaffFields[$columnName]['validators'], $entityData)
                                                   : array(),
                            )
                        );
                        break;
                    }
                }
            }
        }

        // Cross Site Request Forgery protection
        if ($this->scaffOptions['csrfProtected']) {
            $form['elements']['csrf_hash'] = array('hash', array('salt' => 'sea_salt_helps'));
        }

        // Generate create form buttons
        if ($action == self::ACTION_CREATE) {
            foreach ($this->scaffOptions['createButtons'] as $btnId) {
                $form['elements'][$btnId] = array(
                    'submit',
                    array(
                        'label' => $this->buttonLabels[$btnId],
                        'class' => self::ID_TOKEN . '-' . $btnId
                    ),
                );
            }
        } else {
            $form['elements'][self::BUTTON_SAVE] = array(
                'submit',
                array(
                    'label' => $this->buttonLabels[ self::BUTTON_SAVE],
                    'class' => self::ID_TOKEN . '-' . self::BUTTON_SAVE
                ),
            );
        }

        $form['action'] = $this->view->url();

        // Enable rich text editor for necessary fields
        if (count($rteFields)) {
            $this->_loadRichTextEditor($rteFields);
        }

        // Enable date picker
        if (count($datePickerFields)) {
            $this->_loadDatePicker($datePickerFields);
        }

        // Additionally process form
        return $this->_prepareEditForm($form);
    }

    /**
     * Initializes entity search form.
     * @param array $fields list of searchable fields.
     * @return Zend_Form instance of form object
     */
    private function _initSearchForm(array $fields)
    {
        $info               = $this->_getMetadata();
        $metadata           = $info['metadata'];
        $datePickerFields   = array();
        $form               = array();

        foreach ($fields as $columnName) {
            if (isset($metadata[$columnName]['DATA_TYPE'])) {
                $dataType = strtolower($metadata[$columnName]['DATA_TYPE']);
                $fieldType = '';
            } else {
                $dataType = '';
                if (in_array($this->scaffFields[$columnName]['type'], array('date', 'datepicker', 'datetime'))) {
                    $fieldType = 'date';
                } elseif ($this->scaffFields[$columnName]['type'] == 'text') {
                    $fieldType = 'text';
                } else {
                    throw new Zend_Controller_Exception("Fields of type '{$this->scaffFields[$columnName]['type']}' are not searchable.");
                }
            }

            if (preg_match('/^enum/i', $metadata[$columnName]['DATA_TYPE'])
                    || (isset($this->scaffFields[$columnName]['searchOptions'])
                            && is_array($this->scaffFields[$columnName]['searchOptions']))) {
                $options = array();
                // Try to extract options from enum
                if (preg_match_all('/\'(.*?)\'/', $metadata[$columnName]['DATA_TYPE'], $matches)) {
                    foreach ($matches[1] as $match) {
                        $options[$match] = $match;
                    }
                } else {
                    // Or use the specified options
                    $options = $this->scaffFields[$columnName]['searchOptions'];
                }
                $options[''] = 'any';
                ksort($options);

                if (isset($this->scaffFields[$columnName]['type'])
                        && $this->scaffFields[$columnName]['type'] == 'radio') {
                    $elementType = 'radio';
                } else {
                    $elementType = 'select';
                }

                $form['elements'][$columnName] = array(
                    $elementType,
                    array(
                        'multiOptions' => $options,
                        'label' => $this->_getColumnTitle($columnName),
                        'class' => self::ID_TOKEN . '-search-' . $elementType,
                        'value' => ''
                    )
                );
            } elseif (in_array($dataType, array('date', 'datetime', 'timestamp')) || $fieldType == 'date') {
                $form['elements'][$columnName . '_' . self::ID_TOKEN . '_from'] =
                    array(
                        'text', array(
                            'label'         => $this->_getColumnTitle($columnName) . ' from ',
                            'class'         => self::ID_TOKEN . '-search-' . $dataType . $fieldType,
                        )
                    );

                $form['elements'][$columnName . '_' . self::ID_TOKEN . '_to'] =
                    array(
                        'text', array(
                            'label' => ' to ',
                            'class' => self::ID_TOKEN . '-search-' . $dataType . $fieldType,
                        )
                    );

                $datePickerFields[] = $columnName . '_' . self::ID_TOKEN . '_from';
                $datePickerFields[] = $columnName . '_' . self::ID_TOKEN . '_to';
            } elseif (in_array($dataType, array('char', 'varchar')) || $fieldType == 'text') {
                    $length     = isset($this->scaffFields[$columnName]['size'])
                                    ? $this->scaffFields[$columnName]['size'] : '';
                    $maxlength  = isset($this->scaffFields[$columnName]['maxlength'])
                                    ? $this->scaffFields[$columnName]['maxlength'] :
                                        isset($metadata[$columnName]['LENGTH'])
                                            ? $metadata[$columnName]['LENGTH'] : '';

                    $form['elements'][$columnName] = array(
                        'text',
                        array(
                            'class'     => self::ID_TOKEN . '-search-text',
                            'label'     => $this->_getColumnTitle($columnName),
                            'size'      => $length,
                            'maxlength' => $maxlength,
                        )
                    );
            } elseif (in_array($dataType, array('tinyint', 'int', 'integer', 'bool'))) {
                // Determine if the column is a foreign key and build necessary select/multicheckbox field.
                // @todo: use getReference?
                if (count($info['referenceMap']) > 0) {
                    $match = false;
                    $refColumnPartner = false;
                    foreach ($info['referenceMap'] AS $rule => $ruleDetails) {
                        // Try to find the column in list of foreign key columns.
                        if (is_array($ruleDetails['columns']) && ($colId = array_search($columnName, $ruleDetails['columns'])) !== false) {
                            $refColumnPartner = $ruleDetails['refColumns'][$colId];
                            $match = true;
                        } elseif (is_string($ruleDetails['columns']) && $columnName == $ruleDetails['columns']) {
                            $refColumnPartner = is_array($ruleDetails['refColumns']) ?
                                                array_shift($ruleDetails['refColumns']) : $ruleDetails['refColumns'];
                            $match = true;
                        }

                        if ($match === true && $refColumnPartner !== false) {
                            $options = array();
                            $options[""] = "";

                            $depModel = new $ruleDetails['refTableClass']();
                            $namingFieldKey = $refColumnPartner;

                            if (isset($depModel->titleField)) {
                                $namingFieldKey = $depModel->titleField;
                            }

                            foreach ($depModel->fetchAll()->toArray() AS $k => $v) {
                                $key = $v[$refColumnPartner]; // obtain value of partner column
                                if (!isset($options[$key])) {
                                    $options[$key] = $v[$namingFieldKey];
                                }
                            }

                            $form['elements'][$columnName] = array(
                                'select', array(
                                    'multiOptions'  => $options,
                                    'label'         => $this->_getColumnTitle($columnName),
                                    'class'         => self::ID_TOKEN . '-search-select',
                                )
                            );
                            break;
                        }
                    }

                    // Foreign key field has been generated, go to next field
                    if ($match === true) {
                        continue;
                    }
                }

                $form['elements'][$columnName] = array(
                        'checkbox',
                        array(
                            'class' => self::ID_TOKEN . '-search-radio',
                            'label' => $this->_getColumnTitle($columnName),
                        )
                    );
            } else {
                throw new Zend_Controller_Exception("Fields of type $dataType are not searchable.");
            }

            // Allow to search empty records
            if (isset($this->scaffFields[$columnName]['searchEmpty'])) {
                $form['elements']["{$columnName}searchempty"] = array(
                        'checkbox',
                        array(
                            'class' => self::ID_TOKEN . '-search-radio',
                            'label' => $this->_getColumnTitle($columnName) . _(' is empty'),
                        )
                    );
            }
        }

        $form['elements']['submit'] = array(
            'submit',
            array(
                'ignore'   => true,
                'class' => self::ID_TOKEN . '-btn-search',
                'label' => 'Search',
            )
        );

        $form['elements']['reset'] = array(
            'submit',
            array(
                'ignore'   => true,
                'class' => self::ID_TOKEN . '-btn-reset',
                'label' => 'Reset',
                'onclick' => 'ssfResetForm(this.form);'
            ),
        );

        if (count($datePickerFields)) {
            $this->_loadDatePicker($datePickerFields);
        }

        $form['action'] = $this->view->url();

        return $this->_prepareSearchForm($form);
    }

    /**
     * Filters form values making them ready to be used by Zend_Db_Table_Abstract.
     *
     * @param Array $values form values
     * @return Array $values filtered values
     */
    private function _getDbValues(array $values)
    {
        if (count($values) > 0) {
            if (isset($values['csrf_hash'])) {
                unset($values['csrf_hash']);
            }
            unset($values['submit']);
        }

        return $values;
    }

    /**
     * Prepare form values for insertion. Applies field save modifiers
     * and handles many-to-many synthetic fields.
     *
     * @param Array $values initial values
     * @return Array $values modified values
     */
    private function _getDbValuesInsert(array $values)
    {
        $values = $this->_getDbValues($values);
        $relData= array();

        if (count($values) > 0) {
            $info = $this->_getMetadata();
            if (!$this->scaffOptions['pkEditable']) {
                foreach ($info['primary'] AS $primaryKey) {
                    unset($values[$primaryKey]);
                }
            }
        }

        foreach ($values AS $k => $v) {
            // Many-to-many field has to be saved into another table
            if (isset($this->scaffFields[$k]['dependentTable'])) {
                $relData[] = array($this->scaffFields[$k]['dependentTable'], $v);
                unset($values[$k]);
            } else {
                // Apply field modifier if any
                if (isset($this->scaffFields[$k]['saveModifier'])) {
                    $values[$k] = call_user_func($this->scaffFields[$k]['saveModifier'], $v);
                }
            }
        }

        return array($values, $relData);
    }

    /**
     * Prepare form values for update. Applies field save modifiers
     * and handles many-to-many synthetic fields.
     *
     * @param Array $entity original values (before update)
     * @param Array $values new values
     * @return Array modified values in form array($values => Array, $where => String)
     */
    private function _getDbValuesUpdate(array $entity, array $values)
    {
        $values = $this->_getDbValues($values);
        $info   = $this->_getMetadata();
        $where  = array();
        $update = array();
        $relData= array();

        foreach ($values AS $k => $v) {
            // PK used in where clause
            if (in_array($k, $info['primary'])) {
                $where[] = $this->scaffDb->getAdapter()->quoteInto("$k = ?", $entity[$k]);
            }

            if (in_array($k, $info['cols'])) {
                // Normal table field has to be directly saved
                if (!(isset($this->scaffFields[$k]['required']) && $this->scaffFields[$k]['required'] == 'onCreate' && empty($v)))
                    // Apply field modifier if any
                    if (isset($this->scaffFields[$k]['saveModifier'])) {
                        $update[$k] = call_user_func($this->scaffFields[$k]['saveModifier'], $v);
                    } else {
                        $update[$k] = $v;
                    }
            } elseif (isset($this->scaffFields[$k]['dependentTable'])) {
                // Many-to-many field has to be saved into another table
                $relData[] = array($this->scaffFields[$k]['dependentTable'], $v);
            }
        }

        if (count($where) > 0) {
            $where = implode(" AND ", $where);
            return array($update, $where, $relData);
        } else {
            return array(null, null, null);
        }
    }

    /**
     * Prepares the list of records. Optionally applies field listing modifiers.
     *
     * @param Array $entries entries to be displayed
     * @return Array $list resulting list of entries
     */
    private function _prepareDbRecordsList(array $entries)
    {
        $info = $this->_getMetadata();
        $list = array();

        foreach ($entries AS $entry) {
            $keys = array();
            foreach ($entry AS $field => $value) {
                if (is_array($info) && in_array($field, $info['primary'])) {
                    $keys[$field] = $value;
                } elseif (isset($this->scaffFields[$field]['pk']) && $this->scaffFields[$field]['pk']) {
                    $keys[$field] = $value;
                }

                $skip = isset($this->scaffFields[$field]['skip']) ?
                        $this->scaffFields[$field]['skip'] : false;
                if ($skip && ($skip === true || strtolower($skip) == 'list')) {
                    continue;
                }

                // Call list view modifier for specific column if set
                if (isset($this->scaffFields[$field]['listModifier'])) {
                    $row[$field] = call_user_func($this->scaffFields[$field]['listModifier'], $value);
                } else {
                    $row[$field] = $value;
                }
            }

            $row['pkParams'] = $keys;
            $list[] = $row;
        }

        return $list;
    }

    /**
     * Retrieve model table metadata.
     * @return Array
     */
    private function _getMetadata()
    {
        if (is_null($this->scaffMeta)) {
            if ($this->scaffDb instanceof Zend_Db_Table_Abstract) {
                $this->scaffMeta = $this->scaffDb->info();
            } elseif ($this->scaffDb instanceof Zend_Db_Table_Select) {
                $this->scaffMeta = $this->scaffDb->getTable()->info();
            }
        }

        return $this->scaffMeta;
    }

    /**
     * Looks if there is a custom defined name for the column for displaying
     * @param String $columnFieldName
     * @return String $columnLabel
     */
    private function _getColumnTitle($columnName)
    {
        if (isset($this->scaffFields[$columnName]['title'])) {
            return $this->scaffFields[$columnName]['title'];
        } else {
            return ucfirst($columnName);
        }
    }

    /**
     * Looks if there is a custom defined name for the column for displaying
     * @param String $columnFieldName
     * @return String $columnLabel
     */
    private function _getColumnDescription($columnName)
    {
        if (isset($this->scaffFields[$columnName]['description'])) {
            return $this->scaffFields[$columnName]['description'];
        }
        return null;
    }

    /**
     * Additionally handles validators (adds/removes options if needed).
     *
     * @param String $field database field name
     * @param array $validators list of custom validators
     * @param array $entityData entity record
     */
    private function _prepareValidators($field, $validators, $entityData)
    {
        if (is_array($validators)) {
            foreach ($validators as $i => &$validator) {
                // Validation options provided
                if (is_array($validator)) {
                    // Add exclusion when validating existing value
                    if ($validator[0] == 'Db_NoRecordExists') {
                        if ($this->getRequest()->getActionName() == self::ACTION_UPDATE) {
                            $validator[2]['exclude'] = array('field' => $field, 'value' => $entityData[$field]);
                        }
                    }
                }
            }
        } else {
            $validators = array();
        }

        return $validators;
    }

    /**
     * Builds the edition form object. Use this method to apply custom logic like decorators etc.
     *
     * @param array $form form configuration array
     * @return Zend_Form instance of Zend_Form
     */
    protected function _prepareEditForm(array &$form)
    {
        $formObject = new Zend_Form($form);

        // Add required flag
        foreach ($formObject->getElements() as $element) {
            $label = $element->getDecorator('Label');
            if (is_object($label)) {
                $label->setOption('requiredSuffix', ' *');
            }

            // Override default form decorator for certain elements that cause spaces
            if ($element instanceof Zend_Form_Element_Button || $element instanceof Zend_Form_Element_Submit
                    || $element instanceof Zend_Form_Element_Hash || $element instanceof Zend_Form_Element_Hidden) {
                $element->setDecorators(array('ViewHelper'));
            }
        }

        $formObject->setAttrib('class', self::ID_TOKEN . '-edit-form');

        return $formObject;
    }

    /**
     * Builds the search form object. Use this method to apply custom logic like decorators etc.
     *
     * @param array $form form configuration array
     * @return Zend_Form instance of Zend_Form
     */
    protected function _prepareSearchForm(array &$form)
    {
        $formObject = new Zend_Form($form);

        foreach ($formObject->getElements() as $element) {
            // Override default form decorator for certain elements that cause spaces
            if ($element instanceof Zend_Form_Element_Button || $element instanceof Zend_Form_Element_Submit
                    || $element instanceof Zend_Form_Element_Hash || $element instanceof Zend_Form_Element_Hidden) {
                $element->setDecorators(array('ViewHelper'));
            }
        }

        $formObject->setAttrib('class', self::ID_TOKEN . '-search-form');
        return $formObject;
    }

    /**
     * Allows to initialize a JavaScript date picker.
     * Typically you should include here necessary JS files.
     *
     * @param array $fields fields that use date picking
     */
    protected function _loadDatePicker(array $fields)
    {
    }

    /**
     * Allows to initialize a JavaScript rich text editor.
     * Typically you should include here necessary JS files.
     *
     * @param array $fields fields that use rich text editor
     */
    protected function _loadRichTextEditor(array $fields)
    {
    }

    /**
     * The function called every time BEFORE entity is created.
     *
     * @param Zend_Form $form submitted form object
     * @return true if creation must happen or false otherwise
     */
    protected function _beforeCreate(Zend_Form $form, array &$formValues)
    {
        return true;
    }

    /**
     * The function called every time AFTER entity has been created.
     *
     * @param Zend_Form $form submitted form object
     * @param int $insertId just inserted entity's id
     * @return true if automatic redirect must happen and false if user will
     *          redirect manually
     */
    protected function _afterCreate(Zend_Form $form, $insertId)
    {
        return true;
    }

    /**
     * The function called every time BEFORE entity is updated.
     *
     * @param Zend_Form $form submitted form object
     * @param array $formValues values as returned by _getDbValuesUpdate method
     * @return true if update must happen or false otherwise
     */
    protected function _beforeUpdate(Zend_Form $form, array &$formValues)
    {
        return true;
    }

    /**
     * The function called every time AFTER entity has been updated.
     *
     * @param Zend_Form $form submitted form object
     * @return true if automatic redirect must happen and false if user will
     *          redirect manually
     */
    protected function _afterUpdate(Zend_Form $form)
    {
        return true;
    }

    /**
     * The function called every time BEFORE entity is deleted.
     *
     * @param Zend_Db_Table_Row_Abstract $entity record to be deleted
     * @return true if deletion must happen or false otherwise
     */
    protected function _beforeDelete(Zend_Db_Table_Row_Abstract $entity)
    {
        return true;
    }

    /**
     * The function called every time AFTER entity has been deleted.
     *
     * @param Zend_Db_Table_Row_Abstract $entity the deleted record
     * @return true if automatic redirect must happen and false if user will
     *          redirect manually
     */
    protected function _afterDelete(Zend_Db_Table_Row_Abstract $entity)
    {
        return true;
    }
}

?>
