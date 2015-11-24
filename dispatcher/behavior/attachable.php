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

        $mixer = $this->getMixer();

        $aliases = array(
            'com:files.model.attachments'            => array(
                'path' => array('model'),
                'name' => 'attachments'
            ),
            'com:files.database.behavior.attachable' => array(
                'path' => array('database', 'behavior'),
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

        // Set up file controller permission.
        $identifier = $mixer->getIdentifier()->toArray();
        $identifier['path'] = array('controller', 'permission');
        $identifier['name'] = 'attachment';
        $identifier = $this->getIdentifier($identifier)->toString();

        $this->getIdentifier('com:files.controller.file')
             ->getConfig()
             ->append(array('behaviors' => array('permissible' => array('permission' => $identifier))));

        // Make resource tables attachable.
        foreach ($config->resources as $resource)
        {
            $table = $behavior = $config->mixer->getIdentifier()->toArray();

            $table['path'] = array('database', 'table');
            $table['name'] = KStringInflector::pluralize($resource);

            $behavior['path'] = array('database', 'behavior');
            $behavior['name'] = 'attachable';

            $behavior = $this->getIdentifier($behavior);
            $behavior->getConfig()->append(array('container' => $this->_container));

            $this->getIdentifier($table)
                 ->getConfig()
                 ->append(array(
                         'behaviors' => array($behavior)
                     )
                 );
        }
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