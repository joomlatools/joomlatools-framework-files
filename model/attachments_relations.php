<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Relations Attachments Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelAttachments_relations extends KModelDatabase
{
    protected function _buildQuerySelect(KDatabaseQueryInterface $query)
    {
        $state = $this->getState();

        if (!$state->isUnique())
        {
            if ($table = $state->table) {
                $query->where('table = :table');
            }

            if ($row = $state->row) {
                $query->where('row = :row');
            }

            if ($id = $state->files_attachment_id) {
                $query->where('files_attachment_id = :id');
            }

            $query->bind(array('table' => $table, 'row' => $row, 'id' => $id));
        }
    }
}