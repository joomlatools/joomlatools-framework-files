<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnails Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityThumbnails extends ComFilesModelEntityFiles
{
    public function offsetSet($object, $value)
    {
        if ($version = $object->version) {
            $this->_data[$version] = $object;
        } else {
            parent::offsetSet($object, $value);
        }

        return $this;
    }
}