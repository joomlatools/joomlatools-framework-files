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
    protected $_relations_model;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_relations_model = $config->relations_model;

        $this->getState()->insert('table', 'cmd')->insert('row', 'cmd');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('relations_model' => 'attachments_relations'));
        parent::_initialize($config);
    }

    public function getRelationsModel()
    {
        if (!$this->_relations_model instanceof KModelInterface)
        {
            $identifier = $this->_relations_model;

            if (is_string($identifier))
            {
                if (strpos($identifier, '.') === false)
                {
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['name'] = $this->_relations_model;
                }

                $identifier = $this->getIdentifier($identifier);
            }

            $this->_relations_model = $this->getObject($identifier, array(
                'relation_column' => $this->getTable()
                                          ->getIdentityColumn()
            ));
        }

        return $this->_relations_model;
    }

    protected function _buildQueryColumns(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryColumns($query);

        $columns = array('tbl.*', 'container_slug' => 'containers.slug');

        $state = $this->getState();

        if ($state->table && $state->row)
        {
            $columns = array_merge($columns, array(
                'attached_by'      => 'relations.created_by',
                'attached_on'      => 'relations.created_on',
                'attached_by_name' => 'users.name'
            ));
        }

        $query->columns($columns);
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $query->join('files_containers AS containers', 'containers.files_container_id = tbl.files_container_id', 'INNER');

        $state = $this->getState();

        if ($state->row || $state->table)
        {
            $table  = $this->getRelationsModel()->getTable()->getBase();
            $column = $this->getTable()->getIdentityColumn();

            $query->join($table . ' AS relations', 'relations.' . $column . ' = tbl.' . $column, 'INNER')
                  ->join('users AS users', 'relations.created_by = users.id', 'LEFT');
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

    /**
     * Overridden for pushing the container value.
     */
    protected function _actionCreate(KModelContext $context)
    {
        $context->entity->append(array(
            'container' => $context->state->container,
        ));

        return parent::_actionCreate($context);
    }
}