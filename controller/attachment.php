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
    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_container = $config->container;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('container' => sprintf('%s-attachments', $config->object_identifier->getPackage())));
        parent::_initialize($config);
    }

    protected function _beforeAdd(KControllerContextInterface $context)
    {
        $context->getRequest()->getData()->container = $this->_getContainer()->id;
    }

    protected function _getContainer()
    {
        if (!$this->_container instanceof ComFilesModelEntityContainer)
        {
            $this->_container = $this->getObject('com:files.model.containers')->slug($this->_container)->fetch();

            if ($this->_container->isNew()) {
                throw new RuntimeException('Invalid container ' . $this->_container);
            }
        }

        return $this->_container;
    }
}