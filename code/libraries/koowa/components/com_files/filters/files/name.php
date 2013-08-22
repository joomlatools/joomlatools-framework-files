<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Name Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileName extends KFilterAbstract
{
    public function validate($row)
	{
        $value = $this->sanitize($row->name);

		if ($value == '') {
            return $this->_error($this->getObject('translator')->translate('Invalid file name'));
		}
	}

	public function sanitize($value)
	{
		return $this->getObject('com://admin/files.filter.path')->sanitize($value);
	}
}
