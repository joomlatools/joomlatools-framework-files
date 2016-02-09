<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachments JSON View.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesViewAttachmentsJson extends ComFilesViewJson
{
    /**
     * Overridden for setting from extra properties.
     */
    protected function _getEntity(KModelEntityInterface $entity)
    {
        $data = parent::_getEntity($entity);

        $data['file']                  = $entity->file->toArray();
        $data['created_on_timestamp']  = strtotime($entity->created_on);
        $data['attached_on_timestamp'] = strtotime($entity->attached_on);

        return $data;
    }
}