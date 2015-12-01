<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachable Behavior
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseBehaviorAttachable extends KDatabaseBehaviorAbstract
{
    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $attachments = $this->_getAttachmentsModel()->table($this->_getTableName())->row($this->id)->fetch();

        if ($attachments->delete()) {
            $this->_deleteFiles($attachments);
        }
    }

    protected function _deleteFiles($attachments)
    {
        $model = $this->_getModel();

        foreach ($attachments as $attachment)
        {
            if (!$model->container($attachment->container)->name($attachment->name)->count())
            {
                $file = $attachment->file;

                if (!$file->isNew()) {
                    $file->delete();
                }
            }
        }
    }

    public function getAttachments()
    {
        $model = $this->_getModel();

        if (!$this->isNew()) {
            $attachments = $model->table($this->_getTableName())->row($this->{$this->_row_column})->fetch();
        }  else {
            $attachments = $model->fetch();
        }

        return $attachments;
    }

    protected function _getTableName()
    {
        return $this->_getTable()->getBase();
    }

    protected function _getTable()
    {
        $mixer = $this->getMixer();

        if ($mixer instanceof KModelEntityInterface) {
            $table = $mixer->getTable();
        } else {
            $table = $mixer;
        }

        return $table;
    }

    protected function _getModel()
    {
        $mixer = $this->getMixer();

        $identifier = $mixer->getIdentifier()->toArray();

        $identifier['path'] = array('model');
        $identifier['name'] = 'attachments';

        return $this->getObject($identifier);
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