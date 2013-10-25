<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Default Controller
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerDefault extends ComKoowaControllerDefault
{
	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'persistable' => false,
			'limit' => array('max' => 1000)
		));

		parent::_initialize($config);
	}

	public function getRequest()
	{
		$request = parent::getRequest();

		// "e_name" is needed to be compatible with com_content of Joomla
		if ($request->e_name) {
			$request->editor = $request->e_name;
		}

		// "config" state is only used in HMVC requests and passed to the JS application
		if ($this->isDispatched()) {
			unset($request->config);
		}
		
		return $request;
	}

	protected function _actionCopy(KCommandContext $context)
	{
		$data = $this->getModel()->getItem();

		if(!$data->isNew())
		{
			$data->setData(KObjectConfig::unbox($context->data));

			//Only throw an error if the action explicitly failed.
			if($data->copy() === false)
			{
				$error = $data->getStatusMessage();
				$context->setError(new KControllerExceptionActionFailed(
				   $error ? $error : 'Copy Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
				));

			}
			else $context->status = $data->getStatus() === KDatabase::STATUS_CREATED ? KHttpResponse::CREATED : KHttpResponse::NO_CONTENT;
		}
		else $context->setError(new KControllerExceptionNotFound('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	protected function _actionMove(KCommandContext $context)
	{
		$data = $this->getModel()->getItem();

		if(!$data->isNew())
		{
			$data->setData(KObjectConfig::unbox($context->data));

			//Only throw an error if the action explicitly failed.
			if($data->move() === false)
			{
				$error = $data->getStatusMessage();
				$context->setError(new KControllerExceptionActionFailed(
				   $error ? $error : 'Move Action Failed', KHttpResponse::INTERNAL_SERVER_ERROR
				));

			}
			else $context->status = $data->getStatus() === KDatabase::STATUS_CREATED ? KHttpResponse::CREATED : KHttpResponse::NO_CONTENT;
		}
		else $context->setError(new KControllerExceptionNotFound('Resource Not Found', KHttpResponse::NOT_FOUND));

		return $data;
	}

	/**
	 * Overridden method to be able to use it with both model and view controllers
	 */
	protected function _actionGet(KCommandContext $context)
	{
		if ($this->getIdentifier()->name == 'image' || ($this->getIdentifier()->name == 'file' && $this->getRequest()->format == 'html'))
		{
            $this->getObject('translator')->loadLanguageFiles($this->getIdentifier());

			$result = $this->getView()->display();
			return $result;
		}

		return parent::_actionGet($context);
	}
	
	/**
	 * Copied to allow 0 as a limit
	 * 
	 * @param KCommandContext $context
	 */
	protected function _actionBrowse(KCommandContext $context)
	{
	    if($this->isDispatched())
	    {
	        $limit = $this->getModel()->getState()->limit;

	        //If limit is empty use default
	        if(empty($limit) && $limit !== 0) {
	            $limit = $this->_limit->default;
	        }
	
	        //Force the maximum limit
	        if($limit > $this->_limit->max) {
	            $limit = $this->_limit->max;
	        }

            $this->getModel()->getState()->limit = $limit;
	    }
	
	    $data = $this->getModel()->getList();
		return $data;
	}
}
