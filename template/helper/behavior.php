<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Behavior Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperBehavior extends ComKoowaTemplateHelperBehavior
{
    public function plyr($config = array())
    {
        $html = '';

        if (!static::isLoaded('plyr'))
        {
            $html .= $this->getObject('com:files.view.plyr.html')
                         ->getTemplate()
                         ->addFilter('style')
                         ->addFilter('script')
                         ->loadFile('com:files.plyr.default.html')
                         ->render();

            static::setLoaded('plyr');
        }

        return $html;
    }
}