<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachable Dispatcher Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcherBehaviorAttachable extends KBehaviorAbstract
{
    /**
     * The attachments container slug.
     *
     * @var string
     */
    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_container = $config->container;

        $this->_setConfigurations();
        $this->_setAliases();
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'resources' => array(),
            'container' => sprintf('%s-attachments',
                $config->mixer->getIdentifier()->getPackage())
        ));

        parent::_initialize($config);
    }

    protected function _setConfigurations()
    {
        $mixer = $this->getMixer();

        // Set up controller permissions.
        $controllers = array('com:files.controller.file');

        $parts = $mixer->getIdentifier()->toArray();
        $parts['path'] = array('controller');
        $parts['name'] = 'attachment';

        $identifier = $this->getIdentifier($parts);

        // Set attachment controller container.
        $identifier->getConfig()->append(array('container' => $this->_container));

        $controllers[] = $identifier->toString();

        $parts['path'] = array('controller', 'permission');

        $identifier = $this->getIdentifier($parts)->toString();

        foreach ($controllers as $controller)
        {
            $this->getIdentifier($controller)
                 ->getConfig()
                 ->append(array('behaviors' => array('permissible' => array('permission' => $identifier))));
        }

        // Make resource controllers and tables attachable.
        foreach ($this->getConfig()->resources as $resource)
        {
            $subject = $behavior = $mixer->getIdentifier()->toArray();

            // Database layer.
            $subject['path'] = array('database', 'table');
            $subject['name'] = KStringInflector::pluralize($resource);

            $behavior['path'] = array('database', 'behavior');
            $behavior['name'] = 'attachable';

            $identifier = $this->getIdentifier($behavior);
            $identifier->getConfig()->append(array('container' => $this->_container));

            $this->getIdentifier($subject)->getConfig()
                 ->append(array('behaviors' => array($identifier)));

            // Controller layer.
            $subject['path'] = array('controller');
            $subject['name'] = KStringInflector::singularize($resource);

            $behavior['path'] = array('controller', 'behavior');
            $behavior['name'] = 'attachable';

            $this->getIdentifier($subject)->getConfig()
                 ->append(array('behaviors' => array($this->getIdentifier($behavior))));
        }
    }

    protected function _setAliases()
    {
        $mixer = $this->getMixer();

        $aliases = array(
            'com:files.controller.attachment'            => array(
                'path' => array('controller'),
                'name' => 'attachment'
            ),
            'com:files.model.attachments'                => array(
                'path' => array('model'),
                'name' => 'attachments'
            ),
            'com:files.database.behavior.attachable'     => array(
                'path' => array('database', 'behavior'),
                'name' => 'attachable'
            ),
            'com:files.controller.behavior.attachable'   => array(
                'path' => array('controller', 'behavior'),
                'name' => 'attachable'
            ),
            'com:files.controller.permission.attachment' => array(
                'path' => array('controller', 'permission'),
                'name' => 'attachment'
            )
        );

        // Create aliases for attachment classes where required.
        foreach ($aliases as $identifier => $alias)
        {
            $alias = array_merge($mixer->getIdentifier()->toArray(), $alias);

            $manager = $this->getObject('manager');

            // Register the alias if a class for it cannot be found.
            if (!$manager->getClass($alias, false)) {
                $manager->registerAlias($identifier, $alias);
            }
        }
    }

    protected function _beforeGet(KDispatcherContextInterface $context)
    {
        $query = $context->getRequest()->getQuery();

        if ($query->routed && in_array($query->view, array('file', 'files')))
        {
            $this->_forward($context);
            $this->send($context);
        }
    }

    protected function _beforePost(KDispatcherContextInterface $context)
    {
        $query = $context->getRequest()->getQuery();

        if ($query->routed && in_array($query->view, array('file', 'files'))) {
            $this->_forward($context);
        }
    }

    protected function _beforePut(KDispatcherContextInterface $context)
    {
        $this->_beforePost($context);
    }

    protected function _forward(KDispatcherContextInterface $context)
    {
        $context->getRequest()->getQuery()->container = $this->_container;
        $context->param = 'com:files.dispatcher.http';
        $this->forward($context);
    }
}