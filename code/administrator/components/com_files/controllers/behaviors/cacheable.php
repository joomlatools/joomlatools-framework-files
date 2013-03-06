<?php
/**
 * @package     Files
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilesControllerBehaviorCacheable extends ComKoowaControllerBehaviorCacheable
{
	protected $_cache; 
	
	protected $_group_prefix;
	
	public function __construct(KConfig $config) 
	{
		parent::__construct($config);
		
		$this->_group_prefix = $config->group_prefix;
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_LOWEST,
			'group_prefix' => 'com_files.'
		));

		parent::_initialize($config);
	}
	
	protected function _getCache()
	{
		if (!$this->_cache) 
		{
			jimport('joomla.cache.cache');
			
			$app = JFactory::getApplication();
			
			$options = array(
				'caching' 		=> true, 
				'defaultgroup'  => $this->_getGroup(),
				'lifetime' 		=> 60*24*180,
				'cachebase' 	=> $app->getCfg('cache_path'),
				'language' 		=> $app->getCfg('language'),
				'storage'		=> $app->getCfg('cache_handler', 'file')
			);
			
			// 2.5 does this itself
			if (version_compare(JVERSION, '1.6', '<')) {
				$options['lifetime'] *= 60;
			}
			
			$this->_cache = JCache::getInstance('output', $options);
		}
		
		return $this->_cache;
	}
	
	protected function _setOutput(KCommandContext $context)
	{
		if ($this->getRequest()->revalidate_cache) {
			return;
		}

		$cache  = $this->_getCache();
		$key    = $this->_getKey();

		if($data = $cache->get($key))
		{
			$data = unserialize($data);
	
			$context->result = $data['component'];
	
			$this->_output = $context->result;
		}
	}
	
	/**
	 * Store the unrendered view data in the cache
	 *
	 * @param   KCommandContext	A command context object
	 * @return 	void
	 */
	protected function _storeOutput(KCommandContext $context)
	{
		if(empty($this->_output))
		{
			$cache  = $this->_getCache();
			$key    = $this->_getKey();
			
			$data  = array();
			$data['component'] = $context->result;
	
			$cache->store(serialize($data), $key);
		}
	}
	
	protected function _beforeGet(KCommandContext $context)
	{
		if ($this->getView()->getFormat() === 'json') {
			return $this->_setOutput($context);
		}
	}
	
	protected function _afterGet(KCommandContext $context)
	{
		if ($this->getView()->getFormat() === 'json') {
			return $this->_storeOutput($context);
		}
	}
	
	protected function _beforeRead(KCommandContext $context)
	{
	}
	
	protected function _afterRead(KCommandContext $context)
	{
	}
		
	protected function _getGroup()
	{
		$namespace = $this->_group_prefix.($this->getModel()->folder ? md5($this->getModel()->folder) : 'root');

		return $namespace;
	}
	
	protected function _getKey()
	{
	    $view  = $this->getView();
	    $state = $this->getModel()->getState()->toArray();
	    
	    // Empty strings get sent in the URL for dispatched requests 
	    // so we turn them to null before creating the key
	    foreach ($state as $key => $value) {
			if ($value === '') {
				$state[$key] = null;
			}
	    }

	    $key = $view->getName().'-'.$view->getLayout().'-'.$view->getFormat().':'.md5(http_build_query($state));
	    return $key;
	}
}