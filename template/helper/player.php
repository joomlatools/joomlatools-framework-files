<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilesTemplateHelperPlayer extends KTemplateHelperAbstract
{
    protected static $_SUPPORTED_FORMATS = array(
        'audio' => array('aac', 'mp3', 'ogg', 'flac','x-flac', 'wave', 'wav', 'x-wav', 'x-pn-wav'),
        'video' => array('mp4', 'webm', 'ogg')
    );

    protected static $_DECLARED = false;

    protected function _initialize(KObjectConfig $config)
    {
        if (! self::$_DECLARED) {

            $template = $this->getObject('com:files.view.plyr.html')
                            ->getTemplate()
                            ->addFilter('style')
                            ->addFilter('script')
                            ->loadFile('com:files.player.default.html');

            print $template->render();

            self::$_DECLARED = true;
        }

        parent::_initialize($config);
    }
}