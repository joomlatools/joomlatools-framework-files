<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Controller
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerFile extends ComFilesControllerAbstract
{
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.add'  , array($this, 'addFile'));
        $this->registerCallback('before.edit' , array($this, 'addFile'));
	}
	
	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'behaviors' => array('thumbnailable')
		));

		parent::_initialize($config);
	}

	public function addFile(KControllerContextInterface $context)
	{
		if (empty($context->request->data->file) && KRequest::has('files.file.tmp_name'))
		{
			$context->request->data->file = KRequest::get('files.file.tmp_name', 'raw');
			if (empty($context->request->data->name)) {
				$context->request->data->name = KRequest::get('files.file.name', 'raw');
			}

		}
	}
}
