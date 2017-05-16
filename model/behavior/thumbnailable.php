<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnailable Model behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelBehaviorThumbnailable extends KModelBehaviorAbstract
{
    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('thumbnails', 'cmd');
    }

    public function isSupported()
    {
        $result = false;

        if ( $this->getMixer() instanceof ComFilesModelNodes) { // To protect against ::getContainer calls
            $result = true;
        }

        return $result;
    }

    public function getThumbnailsContainer()
    {
        $container = $this->getContainer()->getParameters()->thumbnails_container;

        if ($container && (!$this->_container instanceof ComFilesModelEntityContainer))
        {
            $container = $this->getObject('com:files.model.containers')
                              ->slug($container)
                              ->fetch();

            if ($container->isNew()) {
                throw new RuntimeException('Could not fetch thumbnails container');
            }

            $this->_container = $container->top();
        }

        return $this->_container;
    }

    protected function _afterFetch(KModelContextInterface $context)
    {
        $thumbnails = $this->getState()->thumbnails;

        foreach ($context->entity as $entity)
        {
            $container = $this->getThumbnailsContainer();

            if ($container)
            {
                $entity->thumbnails_container_slug = $container->slug;

                if ($this->getContainer()->getParameters()->thumbnails && $thumbnails)
                {
                    $model = $this->getObject('com:files.model.thumbnails')->container($container->slug)->source($entity->uri);

                    if (!in_array($thumbnails, array('1', 'true'))) {
                        $model->version = $thumbnails;
                    }

                    $thumbnails = $model->fetch();

                    if (!$thumbnails->isNew())
                    {
                        if ($thumbnails->count() == 1) {
                            $thumbnails = $thumbnails->top(); // Un-wrap entity
                        }
                    }
                    else $thumbnails = false;

                    $entity->thumbnail = $thumbnails;
                }
            }
        }
    }
}