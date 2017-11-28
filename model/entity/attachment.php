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

        if (!$this->isNew() && ($model = $this->files_model))
        {
            $model = $this->getObject($this->files_model);

            $key = $model->getTable()->getIdentityColumn();

            $file = $model->id($this->{$key})->fetch()->getIterator()->current();
        }

        return $file;
    }

    public function toArray()
    {
        $data = parent::toArray();

        $file = $this->file;

        if ($file && !$file->isNew()) {
            $data['file'] = $file->toArray();
        }

        $data['created_on_timestamp']  = strtotime($this->created_on);
        $data['attached_on_timestamp'] = strtotime($this->attached_on);

        return $data;
    }
}