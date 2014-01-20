<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * File Thumbnail Behavior
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseBehaviorThumbnail extends KDatabaseBehaviorAbstract
{
    public function saveThumbnail(KCommandInterface $context = null)
    {
        $this->_afterSave($context);
    }

    public function deleteThumbnail(KCommandInterface $context = null)
    {
        $this->_afterDelete($context);
    }

    protected function _afterSave(KCommandInterface $context = null)
    {
        $result = null;
        $available_extensions = array('jpg', 'jpeg', 'gif', 'png');

        if ($this->isImage()
            && $this->getContainer()->getParameters()->thumbnails
            && in_array(strtolower($this->extension), $available_extensions)
        ) {
            $parameters = $this->getContainer()->getParameters();
            $thumbnails_size = isset($parameters['thumbnail_size']) ? $parameters['thumbnail_size'] : array();
            $thumb = $this->getObject('com:files.database.row.thumbnail', array('size' => $thumbnails_size));
            $thumb->source = $this;

            $result = $thumb->save();
        }

        return $result;
    }

    protected function _afterDelete(KCommandInterface $context = null)
    {
        $thumb = $this->getObject('com:files.model.thumbnails')
            ->container($this->container)
            ->folder($this->folder)
            ->filename($this->name)
            ->getItem();

        $result = $thumb->delete();

        return $result;
    }
}