<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilesTemplateHelperPlayer extends KTemplateHelperAbstract
{
    protected static $HTML5_VIDEO_FORMATS = array('mp4', 'webm', 'ogg');

    protected static $HTML5_AUDIO_FORMATS = array('aac', 'mp3', 'ogg', 'flac','x-flac', 'wave', 'wav', 'x-wav', 'x-pn-wav');

    protected static $declared = false;

    /*
    public function load()
    {
        static $html = '';

        if (! self::$declared && $html == '') {
            $html .= '<ktml:script src="media://koowa/com_files/js/plyr/plyr.js" />';
            $html .= '<script>kQuery(function($){plyr.setup()});</script>';
            $html .= '<ktml:style src="media://koowa/com_files/css/plyr.css" />';
        }

        return $html;
    }
    */

    protected function _initialize(KObjectConfig $config)
    {
        $this->addDeclarations();

        parent::_initialize($config);
    }

    public function addDeclarations()
    {
        if (! self::$declared) {

            $document = JFactory::getDocument();
            $document->addScript('media/koowa/com_files/js/plyr/plyr.js');
            $document->addScriptDeclaration('jQuery(function($){plyr.setup()});');
            $document->addStylesheet('media/koowa/com_files/css/plyr.css');

            self::$declared = true;
        }
    }
}