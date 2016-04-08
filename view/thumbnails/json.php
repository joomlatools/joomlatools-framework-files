<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Thumbnails Json View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewThumbnailsJson extends ComFilesViewJson
{
    protected function _fetchData(KViewContext $context)
    {
        $list = $this->getModel()->fetch();
        $results = array();
        foreach ($list as $item) 
        {
        	$key = $item->filename;
        	$results[$key] = $item->toArray();
        }
        ksort($results);

    	$output = array();
        $output['items'] = $results;
        $output['total'] = count($list);

        $this->setContent($output);
    }
}
