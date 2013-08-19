<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Extension Filter
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesFilterFileExtension extends KFilterAbstract
{
	protected $_walk = false;

	protected function _validate($context)
	{
		$allowed = $context->caller->container->parameters->allowed_extensions;
		$value = $context->caller->extension;

		if (is_array($allowed) && (empty($value) || !in_array(strtolower($value), $allowed))) {
			$context->setError($this->getObject('translator')->translate('Invalid file extension'));
			return false;
		}
	}

	protected function _sanitize($value)
	{
		return false;
	}
}
