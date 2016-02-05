<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachment Controller
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerAttachment extends ComKoowaControllerModel
{
    protected $_relations_model;

    protected $_auto_delete;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_auto_delete = $config->auto_delete;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('auto_delete' => true));

        if ($this->getIdentifier()->package != 'files')
        {
            $aliases = array(
                'com:files.model.attachments'                => array(
                    'path' => array('model'),
                    'name' => 'attachments'
                ),
                'com:files.controller.permission.attachment' => array(
                    'path' => array('controller', 'permission'),
                    'name' => 'attachment'
                )
            );

            $manager = $this->getObject('manager');

            foreach ($aliases as $identifier => $alias)
            {
                $alias = array_merge($this->getIdentifier()->toArray(), $alias);

                if (!$manager->getClass($alias, false)) {
                    $manager->registerAlias($identifier, $alias);
                }
            }
        }

        parent::_initialize($config);
    }

    protected function _getRelationsModel()
    {
        if (!$this->_relations_model instanceof KModelInterface)
        {
            // Attachments and relations models MUST belong to the same package.
            $parts = $this->getModel()->getIdentifier()->toArray();

            $parts['name'] .= '_relations';

            $this->_relations_model = $this->getObject($parts);
        }

        return $this->_relations_model;
    }

    protected function _beforeAttach(KControllerContextInterface $context)
    {
        $column = $this->getModel()->getTable()->getIdentityColumn();

        $context->identity_column = $column;

        if (!$context->attachment) {
            $context->attachment = $this->getModel()->fetch();
        }

        if ($context->attachment->isNew()) {
            throw new RuntimeException('Attachment does not exists');
        }
    }

    protected function _actionAttach(KControllerContextInterface $context)
    {
        $model = $this->_getRelationsModel();

        $data   = $context->getRequest()->getData();

        $data[$context->identity_column] = $context->attachment->id;

        $relation = $model->create($context->getRequest()->getData()->toArray());

        if (!$relation->save()) {
            throw new RuntimeException('Could not attach');
        }
    }

    protected function _afterAttach(KControllerContextInterface $context)
    {
        $context->getResponse()->setStatus(KHttpResponse::NO_CONTENT);
    }

    protected function _beforeDetach(KControllerContextInterface $context)
    {
        $this->_beforeAttach($context);
    }

    protected function _actionDetach(KControllerContextInterface $context)
    {
        $model = $this->_getRelationsModel();

        $relation = $model->{$context->identity_column}($context->attachment->id)
                          ->setState($this->getRequest()->getData()->toArray())->fetch();

        if (!$relation->isNew())
        {
            if (!$relation->delete()) {
                throw new RuntimeException('Could not detach');
            }
        }
    }

    protected function _afterDetach(KControllerContextInterface $context)
    {
        if ($this->_auto_delete)
        {
            $model = $this->_getRelationsModel();

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

    public function setView($view)
    {
        $view = parent::setView($view);

        if ($view instanceof KObjectIdentifierInterface && $view->getPackage() !== 'files')
        {
            $manager = $this->getObject('manager');

            if (!$manager->getClass($view, false))
            {
                $identifier = $view->toArray();
                $identifier['package'] = 'files';
                unset($identifier['domain']);

                $manager->registerAlias($identifier, $view);
            }
        }

        return $view;
    }
}