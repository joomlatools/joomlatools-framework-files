<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Default Controller
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
abstract class ComFilesControllerAbstract extends ComKoowaControllerModel
{
	public function getRequest()
	{
		$request = parent::getRequest();

		// "e_name" is needed to be compatible with com_content of Joomla
		if ($request->query->e_name) {
			$request->query->editor = $request->query->e_name;
		}
		
		return $request;
	}

	protected function _actionCopy(KControllerContextInterface $context)
	{
		$data = $this->getModel()->fetch();

		if(!$data->isNew())
		{
			$data->setProperties($context->request->data->toArray());

			//Only throw an error if the action explicitly failed.
			if($data->copy() === false)
			{
				$error = $data->getStatusMessage();
				throw new KControllerExceptionActionFailed($error ? $error : 'Copy Action Failed');
			}
			else $context->status = $data->getStatus() === KDatabase::STATUS_CREATED ? KHttpResponse::CREATED : KHttpResponse::NO_CONTENT;
		}
		else throw new KControllerExceptionResourceNotFound('Resource Not Found');

		return $data;
	}

	protected function _actionMove(KControllerContextInterface $context)
	{
		$data = $this->getModel()->fetch();

		if(!$data->isNew())
		{
			$data->setProperties($context->request->data->toArray());

			//Only throw an error if the action explicitly failed.
			if($data->move() === false)
			{
				$error = $data->getStatusMessage();
				throw new KControllerExceptionActionFailed($error ? $error : 'Move Action Failed');
			}
			else $context->status = $data->getStatus() === KDatabase::STATUS_CREATED ? KHttpResponse::CREATED : KHttpResponse::NO_CONTENT;
		}
		else throw new KControllerExceptionResourceNotFound('Resource Not Found');

		return $data;
	}
}
