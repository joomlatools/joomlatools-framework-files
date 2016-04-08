<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Default Controller Toolbar
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerToolbarFile extends KControllerToolbarActionbar
{
	public function getCommands()
	{
        $this->addUpload(array(
            'label' => 'Upload',
            'attribs' => array(
                'class' => array('btn-success')
            )
        ));

        $this->addNewfolder(array(
            'label' => 'New Folder',
            'icon' => 'icon-new'
        ));

        $this->addSeparator();

        $this->addCopy();
        $this->addMove();
        $this->addDelete();

        $this->addSeparator();

        $this->addRefresh();

        return parent::getCommands();
	}
}
