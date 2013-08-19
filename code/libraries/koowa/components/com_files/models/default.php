<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Default Model
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelDefault extends KModelAbstract
{
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->_state
            ->insert('limit'    , 'int')
            ->insert('offset'   , 'int')
            ->insert('sort'     , 'cmd')
            ->insert('direction', 'word', 'asc')
            ->insert('search'   , 'string')
            // callback state for JSONP, needs to be filtered as cmd to prevent XSS
            ->insert('callback' , 'cmd')

			->insert('container', 'com://admin/files.filter.container', null)
			->insert('folder'	, 'com://admin/files.filter.path', '')
			->insert('name'		, 'com://admin/files.filter.path', '', true)

			->insert('types'	, 'cmd', '')
			->insert('editor'   , 'string', '') // used in modal windows
			->insert('config'   , 'json', '') // used to pass options to the JS application in HMVC
			;
	}

	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'state' => new ComFilesModelState()
		));

		parent::_initialize($config);
	}
}
