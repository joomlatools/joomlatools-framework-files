<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachments Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelAttachments extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()->insert('table', 'cmd')->insert('row', 'cmd');
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $query->columns(array('tbl.*', 'container_slug' => 'containers.slug'));
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $query->join('files_containers AS containers', 'containers.files_container_id = tbl.files_container_id', 'INNER');

        $state = $this->getState();

        if ($state->row || $state->table) {
            $query->join('files_attachments_relations AS relations', 'relations.files_attachment_id = tbl.files_attachment_id', 'INNER');
        }
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if (!$state->isUnique())
        {
            if ($container = $state->container) {
                $query->where('tbl.files_container_id = :container');
            }

            if ($name = $state->name)
            {
                $query->where('tbl.name = :name');
            }

            $query->bind(array('container' => $container, 'name' => $name));

        }

        if ($row = $state->row) {
            $query->where('relations.row = :row')->bind(array('row' => $row));
        }

        if ($table = $state->table) {
            $query->where('relations.table = :table')->bind(array('table' => $table));
        }
    }

    protected function _actionCreate(KModelContext $context)
    {
        $context->entity->append(array(
            'container' => $context->state->container,
        ));

        return parent::_actionCreate($context);
    }
}