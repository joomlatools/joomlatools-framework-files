<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachment Controller Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorAttachment extends KControllerBehaviorAbstract
{
    protected $_controller;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_controller = $config->controller;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('controller' => 'attachment'));

        parent::_initialize($config);
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        $view = $context->getSubject()->getView();

        $view->getConfig()->append(array(
            'config' => array(
                'can_attach' => $this->canAttach(),
                'can_detach' => $this->canDetach()
            )
        ));
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        $entity = $context->result;

        if ($entity instanceof ComFilesModelEntityNode && $entity->getStatus() !== KModelEntityInterface::STATUS_FAILED)
        {
            $container  = $entity->getContainer();
            $controller = $this->_getController();

            $controller->getRequest()->getQuery()->set('container', $container->id);
            $controller->getModel()->container($container->id);

            $controller->add(array('name' => $entity->name));
        }
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

            $this->_controller = $this->getObject($identifier);
        }

        return $this->_controller;
    }
}