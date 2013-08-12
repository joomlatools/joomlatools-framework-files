<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcher extends ComKoowaDispatcher
{
    /**
     * Overloaded execute function to handle exceptions in JSON requests
     */
    public function execute($action, KCommandContext $context)
    {
        try {
            return parent::execute($action, $context);
        }
        catch (Exception $e) {
            $this->_handleException($e);
        }
    }

    protected function _handleException(Exception $e) 
    {
    	if (KRequest::get('get.format', 'cmd') == 'json') 
        {
    		$obj = new stdClass;
    		$obj->status = false;
    		$obj->error  = $e->getMessage();
    		$obj->code   = $e->getCode();

    		// Plupload do not pass the error to our application if the status code is not 200
    		$code = KRequest::get('get.plupload', 'int') ? 200 : ($e->getCode() ? $e->getCode() : 500);

    		header($code.' '.str_replace("\n", ' ', $e->getMessage()), true, $code);

    		echo json_encode($obj);
    		JFactory::getApplication()->close();
    	}
    	else throw $e;
    }
    
	/**
	 * Overloaded to comply with FancyUpload.
	 * It doesn't let us pass AJAX headers so this is needed.
	 */
	public function _actionForward(KCommandContext $context)
	{
		if ($context->result->getStatus() != KDatabase::STATUS_DELETED) {
			if(KRequest::type() == 'FLASH' || KRequest::format() == 'json') {
				$context->result = $this->getController()->execute('display', $context);
			} else {
				parent::_actionForward($context);
			}
		}
		return $context->result;

	}
}
