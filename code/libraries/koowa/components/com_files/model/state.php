<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Config State
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelState extends KModelState
{
	/**
	 * Needed to make sure form filter does not add config to the form action
	 */
	public function getValues($unique = false)
	{
		$data = parent::getValues($unique);
		
		unset($data['config']);
		
		return $data;
	}
}