<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * File Link Template Helper
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperLink extends KTemplateHelperAbstract
{
    public function attachment($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('file' => $config->attachment->file->storage));

        switch($config->attachment->type)
        {
            case 'link':
                if ($config->file->isImage()) {
                    $html = $this->image($config);
                } else {
                    $html = $this->link($config);
                }
                break;
            case 'embedded':
                if ($config->file->isVideo()) {
                    $html = $this->video($config);
                } elseif ($config->isAudio()) {
                    $html = $this->audio($config);
                }
                break;
            default:
                break;
        }

        return $html;
    }

    public function video($config = array())
    {
        $config = new KObjectConfig($config);

        $config->append(array('layout' => 'com:files.file.video.html'));

        return $this->audio($config);
    }

    public function audio($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.audio.html',
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array(
                'data-category' => $this->getIdentifier()->getPackage(),
                'data-title'    => $file->getName(),
                'data-media-id' => 0
            )
        ));

        $html = $this->getTemplate()->createHelper('behavior')->plyr($config);

        $attributes = $this->_prepareAttributes($config->attributes);

        $html .= $this->_render($config->layout, array(
            'url'        => $config->url,
            'file'       => $config->file,
            'attributes' => $attributes
        ));

        return $html;
    }

    public function image($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.image.html',
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array()
        ));

        $html = '';

        if ($file->isImage())
        {
            $thumbnails = $file->thumbnail;

            if ($thumbnails)
            {
                $srcset = array();

                if ($thumbnails->count() > 1)
                {
                    $container = $thumbnails->getIterator()->current()->getContainer();

                    foreach ($container->getParameters()->versions as $label => $settings)
                    {
                        if ($thumbnail = $thumbnails->find($label))
                        {
                            $src = $thumbnail->url ? $thumbnail->url : $thumbnail->path;

                            $srcset[$config->dimension->width] = sprintf('%s %sw', $src, $settings->dimension->width);
                        }
                    }

                    if (count($srcset))
                    {
                        ksort($srcset, SORT_NUMERIC);

                        $srcset = array_values(array_reverse($srcset, true));
                    }

                    $attributes = $this->_prepareAttributes($config->attributes);
                }
            }

            $html = $this->_render($config->layout, array(
                'url'        => $config->url,
                'file'       => $file,
                'attributes' => $attributes,
                'srcset'     => $srcset
            ));
        }

        return $html;
    }

    public function link($config = array())
    {
        $config = new KObjectConfig($config);

        $file = $config->file;

        $config->append(array(
            'layout'     => 'com:files.file.link.html',
            'url'        => sprintf('files://%s/%s', $file->container, $file->path),
            'attributes' => array(),
            'text'      => $file->name
        ));

        $attributes = $this->_prepareAttributes($config->attributes);

        return $this->_render($config->layout, array(
            'url'        => $config->url,
            'file'       => $file,
            'attributes' => $attributes,
            'text'       => $config->text
        ));

    }

    protected function _prepareAttributes($attributes)
    {
        $result = array();

        foreach ($attributes as $key => $value) {
            $result[] = sprintf('%s="%s"', $key, $value);
        }

        return $result;
    }

    protected function _render($layout, $config = array())
    {
        return $this->getTemplate()
                    ->loadFile($layout)
                    ->render($config);
    }
}