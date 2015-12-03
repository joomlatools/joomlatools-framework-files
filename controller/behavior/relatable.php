<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Controller Behavior Relatable
 *
 * Allows relationships add and delete actions through a resource controller.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorRelatable extends KControllerBehaviorAbstract
{
    protected $_properties;

    protected $_model;

    protected $_auto_delete;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_properties  = KObjectConfig::unbox($config->properties);
        $this->_model       = $config->model;
        $this->_auto_delete = $config->auto_delete;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $model = sprintf('%s_relations',  KStringInflector::pluralize($config->mixer->getIdentifier()->getName()));

        $config->append(array('model' => $model, 'auto_delete' => true));

        if (!$config->properties) {
            $config->properties = array('table', 'row');
        }

        parent::_initialize($config);
    }

    protected function _beforeEdit(KControllerContextInterface $context)
    {
        $result = true;

        $entity = $context->getSubject()->getModel()->fetch();

        $properties = array_keys($context->getRequest()->getData()->toArray());

        if (!$entity->isNew() && count(array_intersect($properties, $this->_properties)) == count($this->_properties))
        {
            $this->_add($context);
            $result = false;
        }

        return $result;
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        $properties = array_keys($context->getRequest()->getData()->toArray());

        if ($entity->getStatus !== KModelEntityInterface::STATUS_FAILED &&
            count(array_intersect($properties, $this->_properties)) == count($this->_properties)) {
            $this->_add($context);
        }
    }

    protected function _beforeDelete(KControllerContextInterface $context)
    {
        $result = true;

        $entity = $context->getSubject()->getModel()->fetch();

        $properties = array_keys($context->getRequest()->getQuery()->toArray());

        if (!$entity->isNew() && count(array_intersect($properties, $this->_properties)) == count($this->_properties))
        {
            if ($this->_delete() && $this->_auto_delete)
            {
                $table = $context->getSubject()->getModel()->getTable();

                $model = $this->_getModel();
                $model->getState()->reset();

                if (!$model->{$table->getIdentityColumn()}($entity->id)->count()) {
                    $result = true;
                }
            }
            else $result = false;
        }

        return $result;
    }

    protected function _add(KControllerContextInterface $context)
    {
        $model = $context->getSubject()->getModel();
        $table = $model->getTable();
        $data  = $context->getRequest()->getData();

        $relation = array();

        foreach ($this->_properties as $property) {
            $relation[$property] = $data[$property];
        }

        $entity = $model->fetch();

        $relation = array_merge(array($table->getIdentityColumn() => $entity->id), $relation);

        return $this->_getModel()->create($relation)->save();
    }

    protected function _delete(KControllerContextInterface $context)
    {
        $result = true;

        $model = $this->_getModel();
        $query = $context->getRequest()->getQuery();

        foreach ($this->_properties as $property) {
            $model->{$property} = $query->{$property};
        }

        $relation = $model->fetch();

        if (!$relation->isNew()) {
            $result = $relation->delete();
        }

        return $result;
    }

    protected function _getModel()
    {
        if (!$this->_model instanceof KModelInterface)
        {
            if (!$this->_model instanceof KObjectIdentifierInterface)
            {
                if (strpos($this->_model, '.') === false)
                {
                    $identifier = $this->getMixer()->getIdentifier();

                    $parts = $identifier->toArray();
                    $parts['path'] = array('model');
                    $parts['name'] = $this->_model;

                    $identifier = $this->getIdentifier($parts);
                }
                else $identifier = $this->getIdentifier($this->_model);
            }
            else $identifier = $this->_model;

            $this->_model = $this->getObject($identifier);
        }

        return $this->_model;
    }
}