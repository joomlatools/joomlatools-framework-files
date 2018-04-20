<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnails Model State
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelStateThumbnails extends KModelState
{
    protected $_source_file;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->insert('version', 'cmd')
             ->insert('source', 'url');
    }

    public function set($name, $value = null)
    {
        if ($name == 'source')
        {
            if ($this->get($name) != $value) {
                $this->_source_file = null; // Reset source file if source gets changed
            }

            $parts = parse_url($value);

            $path = isset($parts['host']) ? $parts['host'] : '';
            $path .= isset($path['path']) ? $parts['path'] : '';

            $this->set('name', basename($path) . '.jpg');
            $this->set('folder', dirname($path));
        }

        return parent::set($name, $value);
    }

    public function remove($name)
    {
        if ($name == 'source') {
            $this->_source_file = null;
        }

        return parent::remove($name);
    }

    public function reset($default = true)
    {
        $this->_source_file = null;

        return parent::reset($default);
    }

    public function getSourceFile()
    {
        if ($this->has('source') && !$this->_source_file)
        {
            $parts = explode('://', $this->get('source'));

            if (!in_array($parts[0], stream_get_wrappers()))
            {
                // Assume container instead of stream wrapper

                // INTENTIONAL: not using current folder and name state values to allow overriding the thumbnail
                // location by modyfing folder and name after setting source
                $file = $this->getObject('com:files.model.files')
                             ->container($parts[0])
                             ->folder(dirname($parts[1]))
                             ->name(basename($parts[1]))
                             ->fetch();

                if (!$file->isNew()) {
                    $this->_source_file = $file;
                }
            } else $this->_source_file = $this->getObject('com:files.model.files')
                                              ->create(array('path' => $this->get('source'))); // Use wrapper as source
        }

        return $this->_source_file;
    }
}