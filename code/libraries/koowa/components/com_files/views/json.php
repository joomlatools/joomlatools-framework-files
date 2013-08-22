<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Json View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewJson extends KViewJson
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'list_name' => 'items'
        ));

        parent::_initialize($config);
    }

    protected function _renderItem(KDatabaseRowInterface $row)
    {
        $output = parent::_renderItem($row);
        $status = $row->getStatus() !== KDatabase::STATUS_FAILED;

        $output['status'] = $status;
        if ($status === false){
            $output['error'] = $row->getStatusMessage();
        }

        return $output;
    }
}
