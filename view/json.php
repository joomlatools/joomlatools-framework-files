<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Json View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewJson extends KViewJson
{
    protected function _fetchData(KViewContext $context)
    {
        parent::_fetchData($context);

        $output = $this->getContent();

        if (!$this->isCollection())
        {
            $entity    = $this->getModel()->fetch();
            $status    = $entity->getStatus() !== KDatabase::STATUS_FAILED;

            $output['status'] = $status;

            if ($status === false){
                $output['error'] = $entity->getStatusMessage();
            }

            $this->setContent($output);
        }
    }
}
