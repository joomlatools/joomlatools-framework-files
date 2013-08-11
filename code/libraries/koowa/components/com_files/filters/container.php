<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Filter class for validating containers
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */
class ComFilesFilterContainer extends KFilterAbstract
{
    protected $_walk = false;

    protected function _validate($data)
    {
        if (is_string($data)) {
            return $this->getService('koowa:filter.cmd')->validate($value);
        }
        else if (is_object($data)) {
            return true;
        }

        return false;
    }

    protected function _sanitize($data)
    {
        if (is_string($data)) {
            return $this->getService('koowa:filter.cmd')->sanitize($data);
        }
        else if (is_object($data)) {
            return $data;
        }

        return null;
    }
}
