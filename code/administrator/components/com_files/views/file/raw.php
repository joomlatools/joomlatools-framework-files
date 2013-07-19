<?php
/**
 * @package     Files
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
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
            $translator = $this->getService('translator')->getTranslator($this->getIdentifier());
            throw new KViewException($translator->translate('File not found'));
        }

        return parent::display();
    }
}