<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Attachment Model Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityAttachment extends KModelEntityRow
{
    /**
     * Attachment file getter.
     *
     * @return KModelEntityInterface
     */
    public function getPropertyFile()
    {
        $file = null;

        if (!$this->isNew())
        {
            if (!$this->container_slug)
            {
                $container = $this->getObject('com:files.model.containers')->id($this->container)->fetch();

                if ($container->isNew()) throw new RuntimeException('Container does not exists');

                $this->container_slug = $container->slug;
            }

            $file = $this->getObject('com:files.model.files')
                         ->container($this->container_slug)
                         ->name($this->name)
                         ->thumbnails(true)
                         ->fetch()
                         ->getIterator()
                         ->current();
        }

        return $file;
    }

    public function delete()
    {
        $file = $this->file;

        if ($result = parent::delete()) {
            if (!$file->isNew()) $file->delete();
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $file = $this->file;

        if ($file && !$file->isNew()) {
            $data['file']      = $file->toArray();
        }

        $data['created_on_timestamp']  = strtotime($this->created_on);
        $data['attached_on_timestamp'] = strtotime($this->attached_on);

        return $data;
    }
}