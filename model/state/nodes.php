<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Nodes Model State
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelStateNodes extends KModelState
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->insert('uri', 'url');
    }

    public function set($name, $value = null)
    {
        if ($name == 'uri')
        {
            $parts = parse_url($value);

            $path = isset($parts['host']) ? $parts['host'] : '';
            $path .= isset($parts['path']) ? $parts['path'] : '';

            $this->set('name', basename($path));
            $this->set('folder', dirname($path));

            $wrappers = array_merge(stream_get_wrappers(), array('file'));

            if (!in_array($parts['scheme'], $wrappers)) {
                $this->set('container', basename($parts['scheme']));
            }
        }

        return parent::set($name, $value);
    }
}