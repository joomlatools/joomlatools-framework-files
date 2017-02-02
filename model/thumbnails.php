<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnails Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelThumbnails extends ComFilesModelFiles
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('version', 'cmd');
    }

    protected function _actionCreate(KModelContext $context)
    {
        $parameters = $this->getContainer()->getParameters();

        $entity = $context->getEntity();

        if ($version = $entity->version)
        {
            if ($config = $parameters->versions->{$version})
            {
                $entity->name      = $version . '-' . $entity->name;
                $entity->dimension = $config->dimension->toArray();
                $entity->crop      = $config->crop;
            }
        }
        else
        {
            if ($dimension = $parameters->dimension) {
                $entity->dimension = $dimension;
            }

            if (isset($parameters->crop)) {
                $entity->crop = $parameters->crop;
            }
        }

        return parent::_actionCreate($context);
    }

    protected function _actionFetch(KModelContext $context)
    {
        $result = parent::_actionFetch($context);

        $state      = $this->getState();
        $parameters = $this->getContainer()->getParameters();

        foreach ($result as $entity)
        {
            if ($version = $state->version)
            {
                $versions = (array) $version;

                foreach ($versions as $version)
                {
                    if (strpos($entity->name, $version) === 0) {
                        break;
                    }
                }

                $config = $parameters->versions->{$version};

                $entity->dimension = $config->dimension;
                $entity->crop = $config->crop;
            }
            else
            {
                if ($dimension = $parameters->dimension) {
                    $entity->dimension = $dimension;
                }

                if (isset($parameters->crop)) {
                    $entity->crop = $parameters->crop;
                }
            }
        }

        return $result;
    }

    public function iteratorFilter($path)
    {
        $state     = $this->getState();
        $filename  = ltrim(basename(' '.strtr($path, array('/' => '/ '))));

        if ($filename && $filename[0] === '.') {
            return false;
        }

        if ($name = $state->name)
        {
            $names = array();

            if ($version = $state->version)
            {
                $versions = (array) $version;

                foreach ($versions as $version) {
                    $names[] = $version . '-' . $name;
                }
            }
            else $names[] = $name;

            if (!in_array($filename, $names)) {
                return false;
            }
        }

        if ($state->search && stripos($filename, $state->search) === false) {
            return false;
        }
    }
}
