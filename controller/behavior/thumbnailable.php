<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnailable Controller Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerBehaviorThumbnailable extends KControllerBehaviorAbstract
{
    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.browse', '_setThumbnails');

        $this->_container = $config->container;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('container' => 'fileman-images'));
        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($this->_canGenerateThumbnails()) {
            $this->_generateThumbnails($context->result);
        }
    }

    protected function _canGenerateThumbnails()
    {
        $parameters = $this->_getFilesContainer()->getParameters();

        return (bool) $parameters->thumbnails;
    }

    protected function _getFilesContainer()
    {
        return $this->getModel()->getContainer();
    }

    protected function _generateThumbnails(KModelEntityInterface $entity, $size = null)
    {
        $thumbnails = $this->getObject('com:files.model.entity.thumbnails');

        if ($entity instanceof ComFilesModelEntityFile) {
            $file = $entity;
        } else {
            $file = $this->_getFile($entity);
        }

        if ($file->isImage())
        {
            $parameters = $this->_getContainer()->getParameters();

            $model = $this->getObject('com:files.model.thumbnails')
                          ->container($this->_getContainer()->slug)
                          ->source($file->uri);

            if ($versions = $parameters->versions->toArray())
            {
                $versions = array_keys($versions);

                if ($size) {
                    $versions = array_intersect(array_keys($versions), (array) $size);
                }

                foreach ($versions as $version)
                {
                    $thumbnail = $model->version($version)->create();

                    if ($thumbnail->save()) {
                        $thumbnails->insert($thumbnail);
                    }
                }
            }
            else
            {
                $thumbnail = $model->create();

                if ($thumbnail->save()) {
                    $thumbnails->insert($thumbnail);
                }
            }
        }

        return $thumbnails;
    }

    protected function _getContainer()
    {
        if (!$this->_container instanceof ComFilesModelEntityContainer)
        {
            $container = $this->getObject('com:files.model.containers')->slug($this->_container)->fetch();

            if (!$container->isNew()) {
                $this->_container = $container->top();
            }
        }

        return $this->_container;
    }

    protected function _getFile(KModelEntityInterface $entity)
    {
        return $entity;
    }

    protected function _afterDelete(KControllerContextInterface $context)
    {
        $entities = $context->result;

        foreach ($entities as $entity)
        {
            $file = $this->_getFile($entity);

            $controller = $this->getObject('com:files.controller.thumbnail')
                               ->container($this->_getContainer()->slug)
                               ->folder($file->folder)
                               ->name($file->name);

            $parameters = $this->_getContainer()->getParameters();

            if ($versions = $parameters->versions) {
                $controller->version(array_keys($versions->toArray()));
            }

            $thumbnails = $controller->browse();

            if ($thumbnails->count()) {
                $thumbnails->delete();
            }
        }
    }

	protected function _setThumbnails(KControllerContextInterface $context)
	{
        $request = $context->getRequest();
        $query   = $request->getQuery();

        if (is_bool($query->get('thumbnails', 'raw'))) {
            $thumbnails = $query->get('thumbnails', 'boolean') ? 'true' : 'false';
        } else {
            $thumbnails = $query->get('thumbnails', 'cmd');
        }

        if ($thumbnails && $this->_canGenerateThumbnails())
        {
            if ($thumbnails != 'true') {
                $version = $thumbnails;
            }

            $parameters = $this->_getContainer()->getParameters();
            $result     = $context->result;

            foreach ($result as $entity)
            {
                $file = $this->_getFile($entity);

                if ($entity instanceof ComFilesModelEntityFile && $entity->isImage())
                {
                    $controller = $this->getObject('com:files.controller.thumbnail')
                                       ->container($this->_getContainer()->slug)
                                       ->source($file->uri);

                    if (isset($version)) {
                        $controller->version($version);
                    }

                    $thumbnails = $controller->browse();

                    if (($versions = $parameters->versions) && !isset($version))
                    {
                         if ($thumbnails->count() !== count($versions)) {
                             $thumbnails = $this->_generateThumbnails($file); // Generate missing thumbnails
                         }
                    }
                    elseif ($thumbnails->isNew()) $thumbnails = $this->_generateThumbnails($file, isset($version) ? $version : null);

                    if (!$thumbnails->isNew())
                    {
                        if ($request->getFormat() == 'json') {
                            $thumbnails = $thumbnails->toArray();
                        } elseif ($thumbnails->count() == 1) {
                            $thumbnails = $thumbnails->top(); // Un-wrap entity
                        }
                    }
                    else $thumbnails = false;
                }
                else $thumbnails = false;

                $entity->thumbnail = $thumbnails;
            }
        }
	}
}