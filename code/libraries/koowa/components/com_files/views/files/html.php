<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Files Html View
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesViewFilesHtml extends ComKoowaViewHtml
{
	protected function _initialize(KObjectConfig $config)
	{
		$config->auto_fetch = false;

		parent::_initialize($config);
	}

    public function fetchData(KViewContext $context)
	{
	    $state     = $this->getModel()->getState();
        $container = $this->getModel()->getContainer();

        $config = array(
            'router' => array(
                'defaults' => (object) array(
                    'option' => 'com_'.substr($container->slug, 0, strpos($container->slug, '-')),
                    'routed' => '1'
                )
            ),
            'initial_response' => true
        );

	    if (is_array($state->config)) {
            $config = array_merge_recursive($config, $state->config);
        }

        if ($config['initial_response'] === true)
        {
            $query = $state->getValues();
            unset($query['config']);
            $query['thumbnails'] = $this->getModel()->getContainer()->parameters->thumbnails;

            $config['initial_response'] = $this->getObject('com:files.controller.node', array('query' => $query))
                ->format('json')
                ->render();
        }

        $state->config = $config;

		$context->data->sitebase  = trim(JURI::root(), '/');
		$context->data->token     = JSession::getFormToken();
		$context->data->container = $container;

		parent::fetchData($context);
	}
}
