<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Proxy Controller
 *
 * Used to perform cross origin HEAD request calls on resources to see if they exist, and if exists then also pass the
 * Content-length header
 *
 * @author  Stian Didriksen <https://github.com/stipsan>
 * @package Koowa\Component\Files
 */
 class ComFilesControllerProxy extends ComFilesControllerAbstract
{
	public function _actionRender(KControllerContextInterface $context)
	{
		$data = array(
			'url' => $this->_request->url, 
			'content-length' => false
		);

		if (!function_exists('curl_init')) {
			throw new RuntimeException('Curl library does not exist', KHttpResponse::SERVICE_UNAVAILABLE);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $data['url']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS,		 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 		 20);
		//CURLOPT_NOBODY changes the request from GET to HEAD
		curl_setopt($ch, CURLOPT_NOBODY, 		 true);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new RuntimeException('Curl Error: '.curl_error($ch), KHttpResponse::SERVICE_UNAVAILABLE);
		}

		$info = curl_getinfo($ch);
		if (isset($info['http_code']) && $info['http_code'] != 200) {
			throw new RuntimeException($data['url'].' Not Found', $info['http_code']);
		}
		if (isset($info['download_content_length'])) {
			$data['content-length'] = $info['download_content_length'];
		}


		curl_close($ch);

		return json_encode($data);
	}
}
