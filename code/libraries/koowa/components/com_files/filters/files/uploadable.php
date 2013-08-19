<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */
/**
 * File Uploadble Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileUploadable extends KFilterAbstract
{
	protected $_walk = false;

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		$this->addFilter($this->getService('com://admin/files.filter.file.name'), KCommand::PRIORITY_HIGH);

		$this->addFilter($this->getService('com://admin/files.filter.file.extension'));
		$this->addFilter($this->getService('com://admin/files.filter.file.mimetype'));
		$this->addFilter($this->getService('com://admin/files.filter.file.size'));
	}

	protected function _validate($context)
	{

	}

	protected function _sanitize($context)
	{

	}
}
