<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Container Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterContainer extends KFilterAbstract
{
    public function validate($data)
    {
        if (is_string($data)) {
            return $this->getObject('koowa:filter.cmd')->validate($data);
        } else if (is_object($data)) {
            return true;
        }

        return false;
    }

    public function sanitize($data)
    {
        if (is_string($data)) {
            return $this->getObject('koowa:filter.cmd')->sanitize($data);
        } else if (is_object($data)) {
            return $data;
        }

        return null;
    }
}
