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
    /**
     * The identifier column name of the attachable table.
     *
     * @var string
     */
    protected $_row_column;

    protected $_container;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_row_column = $config->row_column;

        $this->_container = $config->container;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'row_column' => 'id',
            'container'  => sprintf('%s-attachments', $config->mixer->getIdentifier()->getPackage())
        ));
        parent::_initialize($config);
    }

    protected function _afterInsert(KDatabaseContextInterface $context)
    {
        $entity = $context->data;

        if (empty($entity->attachments)) {
            $entity->attachments = array();
        }

        $attachments = (array) $entity->attachments;

        $model = $this->_getAttachmentsModel();

        $attached = $model->table($this->_getTableName())->row($this->{$this->_row_column})->fetch();

        $ignore = array();

        foreach ($attached as $attachment)
        {
            if (!in_array($attachment->name, $attachments)) {
                $attachment->delete();
            } else {
                $ignore[] = $attachment->name;
            }
        }

        $container = $this->_getContainer();

        foreach ($attachments as $attachment)
        {
            if (!in_array($attachment, $ignore))
            {
                $model->create(array(
                    'container' => $container->id,
                    'table'     => $this->_getTableName(),
                    'row'       => $this->{$this->_row_column},
                    'name'      => $attachment
                ))->save();
            }
        }
    }

    protected function _afterUpdate(KDatabaseContextInterface $context)
    {
        $this->_afterInsert($context);
    }

    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $this->_getAttachmentsModel()->table($this->_getTableName())->row($this->{$this->_row_column})->fetch()->delete();
    }

    public function getAttachments()
    {
        $model = $this->_getAttachmentsModel();

        if (!$this->isNew()) {
            $attachments = $model->table($this->_getTableName())->row($this->{$this->_row_column})->fetch();
        }  else {
            $attachments = $model->fetch();
        }

        return $attachments;
    }

    protected function _getTableName()
    {
        $mixer = $this->getMixer();

        if ($mixer instanceof KModelEntityInterface) {
            $table = $mixer->getTable();
        } else {
            $table = $mixer;
        }

        return $table->getBase();
    }

    protected function _getAttachmentsModel()
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