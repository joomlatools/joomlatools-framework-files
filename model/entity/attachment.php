<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachment Model Entity
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityAttachment extends KModelEntityRow
{
    public function getPropertyFile()
    {
        return $this->getObject('com:files.model.files')->container($this->container_slug)->name($this->name)->fetch();
    }

    public function delete()
    {
        $result = parent::delete();

        if ($result)
        {
            // This is the best model guess there is ...
            $parts = $this->getIdentifier()->toArray();

            $parts['path'] = array('model');
            $parts['name'] = 'attachments';

            $model =  $this->getObject($parts);

            $attachments = $model->container($this->container)
                                 ->name($this->name)
                                 ->count();

            if (!$attachments)
            {
                $file = $this->file;

                if (!$file->isNew()) {
                    $file->delete();
                }
            }
        }

        return $result;
    }
}