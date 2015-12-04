<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachable Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorAttachable extends KControllerBehaviorAbstract
{
    protected $_controller;

    protected $_model;

    protected $_auto_delete;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller  = $config->controller;
        $this->_model       = $config->model;
        $this->_auto_delete = $config->auto_delete;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('controller' => 'attachment', 'model' => 'attachments_relations', 'auto_delete' => true));

        parent::_initialize($config);
    }

    protected function _beforeAttach(KControllerContextInterface $context)
    {
        $entity = $context->getSubject()->getModel()->fetch();

        if ($entity->isNew()) {
            throw new RuntimeException('Entity does not exists');
        }

        $data = $context->getRequest()->getData();

        if (!$data->attachment) {
            throw new RuntimeException('Attachment missing');
        }

        $context->entity = $entity;
    }

    protected function _beforeDetach(KControllerContextInterface $context)
    {
        $this->_beforeAttach($context);

        $data = $context->getRequest()->getData();

        $controller= $this->_getController();

        $attachment = $controller->getModel()->name($data->attachment)->fetch();

        if ($attachment->isNew()) {
            throw new RuntimeException('Attachment does not exists');
        }

        $context->attachment = $attachment;
    }

    protected function _actionAttach(KControllerContextInterface $context)
    {
        $request = $context->getRequest();
        $data    = $request->getData();
        $entity  = $context->entity;

        $controller = $this->_getController();

        $attachment = $controller->getModel()->name($data->attachment)->fetch();

        if ($attachment->isNew())
        {
            $attachment = $controller->getModel()->create();

            if (!$attachment->save()) {
                throw new RuntimeException('Attachment could not be saved');
            }
        }

        $model = $this->_getModel();

        $column = $controller->getModel()->getTable()->getIdentityColumn();

        $relation = $model->create(array(
            $column => $attachment->id,
            'table' => $entity->getTable()->getBase(),
            'row'   => $entity->id
        ));

        if ($relation->save() === false) {
            throw new RuntimeException('Relation could not be saved');
        }

        return $relation;
    }

    protected function _actionDetach(KControllerContextInterface $context)
    {
        $identity_column = $this->_getController()->getModel()->getTable()->getIdentityColumn();
        $model           = $this->_getModel();
        $state           = $model->getState();
        $entity          = $context->entity;

        $state->setValues(array(
            $identity_column => $context->attachment->id,
            'table'          => $entity->getTable()->getBase(),
            'row'            => $entity->id
        ));

        $relation = $model->fetch();

        if (!$relation->isNew())
        {
            if ($relation->delete() === false) {
                throw new RuntimeException(('Relation could not be deleted'));
            }
        }

        $context->identity_column = $identity_column;

        return $relation;
    }

    protected function _afterAttach(KControllerContextInterface $context)
    {
        $context->getResponse()->setStatus(KHttpResponse::NO_CONTENT);
    }

    protected function _afterDetach(KControllerContextInterface $context)
    {
        if ($this->_auto_delete)
        {
            $model = $this->_getModel();

            $model->getState()->reset();

            if (!$model->{$context->identity_column}($context->attachment->id)->count())
            {
                if (!$context->attachment->delete()) {
                    throw new RuntimeException(('Attachment could not be deleted'));
                }
            }
        }

        $this->_afterAttach($context);
    }

    protected function _getController()
    {
        if (!$this->_controller instanceof KControllerInterface)
        {
            $mixer = $this->getMixer();

            if (!$this->_controller instanceof KObjectIdentifierInterface)
            {
                if (strpos($this->_controller, '.') === false)
                {
                    $parts         = $mixer->getIdentifier()->toArray();
                    $parts['name'] = $this->_controller;

                    $identifier = $this->getIdentifier($parts);
                } else $identifier = $this->getIdentifier($this->_controller);
            } else $identifier = $this->_controller;

            $request = $this->getObject('lib:controller.request', array(
                'query' => array(
                    'container' => $mixer->getRequest()->getQuery()->container
                )
            ));

            $this->_controller = $this->getObject($identifier, array('request'  => $request));
        }

        return $this->_controller;
    }

    protected function _getModel()
    {
        if (!$this->_model instanceof KModelInterface)
        {
            $mixer = $this->getMixer();

            if (!$this->_model instanceof KObjectIdentifierInterface)
            {
                if (strpos($this->_model, '.') === false)
                {
                    $parts         = $mixer->getIdentifier()->toArray();
                    $parts['path'] = array('model');
                    $parts['name'] = $this->_model;

                    $identifier = $this->getIdentifier($parts);
                } else $identifier = $this->getIdentifier($this->_model);
            } else $identifier = $this->_model;

            $this->_model = $this->getObject($identifier);
        }

        return $this->_model;
    }

}