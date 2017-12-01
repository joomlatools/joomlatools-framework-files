<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Route Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperRoute extends KTemplateHelperAbstract
{
    public function token($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('expire' => '+24 hours'));

        if (!config->secret) throw new InvalidArgumentException('Missing secret property for JWT');

        $token = $this->getObject('lib:http.token');

        $date = $this->getObject('date');

        $token->setExpireTime($date->modify($config->expire));

        return $token->sign($config->secret);
    }
}