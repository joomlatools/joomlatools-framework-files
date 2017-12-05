<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

/**
 * Url Template Filter
 *
 * Filter allows to create url schemes that are replaced on compile and render.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComFilesTemplateFilterUrl extends KTemplateFilterAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('base_path' => $this->getObject('request')->getBasePath()));

        parent::_initialize($config);
    }

    public function filter(&$text)
    {
        $pattern = '~files://(.+)/([^\s"\']+?)~isU';

        $matches = array();

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
           $container = $this->getObject('com:files.model.containers')->slug($match[1])->fetch();

            if (!$container->isNew())
            {
                if ($base = $this->_getBasePath($container))
                {
                    $url = sprintf('/%s/%s', $base, $match[2]);

                    $text = str_replace($match[0], $this->_getCleanBasePath($url), $text);
                }
            }
        }
    }

    protected function _getBasePath($container)
    {
        return $this->_getCleanBasePath(trim(sprintf('%s/%s', $this->getConfig()->base_path, $container->path), '/'));
    }

    protected function _getCleanBasePath($path)
    {
        $folders = explode('/', $path);

        foreach ($folders as &$folder) {
            $folder = rawurlencode($folder);
        }

        return implode('/', $folders);
    }
}