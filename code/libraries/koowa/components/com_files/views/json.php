<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Nodes Json View Class
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */

class ComFilesViewJson extends KViewJson
{
    protected function _initialize(KConfig $config)
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
