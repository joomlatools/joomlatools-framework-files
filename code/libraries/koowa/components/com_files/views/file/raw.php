<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

class ComFilesViewFileRaw extends KViewFile
{
    public function display()
    {
        $file = $this->getModel()->getItem();

        $this->path = $file->fullpath;
        $this->filename = $file->name;
        $this->mimetype = $file->mimetype ? $file->mimetype : 'application/octet-stream';
        if ($file->isImage() || $file->extension === 'pdf') {
            $this->disposition = 'inline';
        }

        if (!file_exists($this->path)) {
            throw new RuntimeException($this->getService('translator')->translate('File not found'));
        }

        return parent::display();
    }
}
