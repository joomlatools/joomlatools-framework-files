<?php
/**
 * @package     Files
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilesControllerBehaviorCacheable extends ComDefaultControllerBehaviorCacheable
{
    /**
     * Cache object
     *
     * @var JCache
     */
    protected $_cache;

    /**
     * Cache group
     *
     * @var string
     */
    protected $_group = '';

    /**
     * If true, behavior only clears the cache after add/edit/delets and do not store new data
     *
     * @var boolean
     */
    protected $_only_clear = false;

    /**
     * Constructor
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->_group = $config->group;

        $this->_only_clear = $config->only_clear;
	}

    /**
     * @param KConfig $config
     */
    protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'priority' => KCommand::PRIORITY_LOWEST,
			'group' => 'com_files',
            'only_clear' => false // If true, behavior only clears the cache after add/edit/delets and do not store new data
		));

		parent::_initialize($config);
	}

    /**
     * Get configuration value from the application
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    protected function _getConfig($key, $default = null) {
        return JFactory::getApplication()->getCfg($key, $default);
    }

    /**
     * Create a JCache instance
     *
     * @return JCache
     */
    protected function _getCache()
	{
		if (!$this->_cache) 
		{
			jimport('joomla.cache.cache');
			
			$options = array(
				'caching' 		=> true, 
				'defaultgroup'  => $this->_getGroup(),
				'lifetime' 		=> 60*24*180,
				'cachebase' 	=> $this->_getConfig('cache_path'),
				'language' 		=> $this->_getConfig('language'),
				'storage'		=> $this->_getConfig('cache_handler', 'file')
			);

			// 2.5 does this itself
			if (version_compare(JVERSION, '1.6', '<')) {
				$options['lifetime'] *= 60;
			}
			
			$this->_cache = JCache::getInstance('output', $options);
		}
		
		return $this->_cache;
	}

    /**
     * Clears group cache
     *
     * @return boolean
     */
    protected function _cleanCache()
    {
        return $this->_getCache()->clean($this->_getGroup());
    }

    /**
     * Set the event output from the cache
     *
     * @param KCommandContext $context
     */
    protected function _setOutput(KCommandContext $context)
	{
		$cache  = $this->_getCache();
		$key    = $this->_getKey();
        $data   = $cache->get($key);

		if ($data)
		{
			$data = unserialize($data);
	
			$context->result = $data['component'];
	
			$this->_output = $context->result;
		}
	}
	
	/**
	 * Store the unrendered view data in the cache
	 *
	 * @param   KCommandContext $context	A command context object
	 * @return 	void
	 */
	protected function _storeOutput(KCommandContext $context)
	{
		if (empty($this->_output))
		{
			$cache  = $this->_getCache();
			$key    = $this->_getKey();
			
			$data  = array();
			$data['component'] = $context->result;

			$cache->store(serialize($data), $key);
		}
	}

    /**
     * Sets the output for JSON requests from cache if possible
     *
     * Also cleans cache if revalidate_cache property is set in request
     *
     * @param KCommandContext $context
     */
    protected function _beforeGet(KCommandContext $context)
	{
		if ($this->getView()->getFormat() === 'json' && $this->_only_clear === false)
        {
            if ($this->getRequest()->revalidate_cache) {
                $this->_cleanCache();
            }
            else {
                $this->_setOutput($context);
            }
		}
	}

    /**
     * Stores the cache output for JSON requests
     *
     * @param KCommandContext $context
     */
    protected function _afterGet(KCommandContext $context)
	{
		if ($this->getView()->getFormat() === 'json' && $this->_only_clear === false) {
			$this->_storeOutput($context);
		}
	}

    /**
     * Overrridden to not run caching on read actions
     *
     * @param KCommandContext $context
     */
    protected function _beforeRead(KCommandContext $context)
	{
	}

    /**
     * Overrridden to not run caching on read actions
     *
     * @param KCommandContext $context
     */
	protected function _afterRead(KCommandContext $context)
	{
	}

    /**
     * Returns cache group name
     *
     * @return string
     */
    protected function _getGroup()
	{
		return $this->_group;
	}

    /**
     * Returns cache key
     *
     * Converts empty strings to null in state data before creating the key
     *
     * Keys are formatted as: folder:md5(state)
     *
     * @return string
     */
    protected function _getKey()
	{
	    $state = $this->getModel()->getState()->toArray();
	    
	    // Empty strings get sent in the URL for dispatched requests 
	    // so we turn them to null before creating the key
	    foreach ($state as $key => $value) {
			if ($value === '') {
				$state[$key] = null;
			}
	    }

	    $key = $this->getModel()->folder.':'.md5(http_build_query($state));

	    return $key;
	}
}