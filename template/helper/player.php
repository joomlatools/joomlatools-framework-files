<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Player Template Helper
 *
 * @author  Rastin Mehr <https://github.com/rmdstudio>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperPlayer extends KTemplateHelperAbstract
{
    protected static $_SUPPORTED_FORMATS = array(
        'audio' => array('aac', 'mp3', 'ogg', 'flac','x-flac', 'wave', 'wav', 'x-wav', 'x-pn-wav'),
        'video' => array('mp4', 'webm', 'ogg')
    );

    public function load($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'download' => false
        ))->append(array(
            'options' => [
                'play-large',   // The large play button in the center
                'play',         // Play/pause playback
                'progress',     // The progress bar and scrubber for playback and buffering
                'current-time', // The current time of playback
                'mute',         // Toggle mute
                'volume',       // Volume control
                'fullscreen'    // Toggle fullscreen
            ]
            ));

        if ($config->download) {
            $config->options->append(['download']); // Show a download button with a link to either the current source or a custom URL you specify in your options
        }

        static $imported = false;

        $html = '';

        if (!$imported)
        {
            $data = array('controls' => KObjectConfig::unbox($config->options));
            $html = $this->getObject('com:files.view.plyr.html')
                ->getTemplate()
                ->addFilter('style')
                ->addFilter('script')
                ->loadFile('com:files.player.default.html')
                ->render($data);

            $imported = true;
        }

        return $html;
    }
}