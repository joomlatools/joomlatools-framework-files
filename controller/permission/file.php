<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

class ComFilesControllerPermissionFile extends KControllerPermissionAbstract
{
    public function canMove()
    {
        return $this->canDelete() && $this->canAdd();
    }

    public function canCopy()
    {
        return $this->canAdd();
    }
}