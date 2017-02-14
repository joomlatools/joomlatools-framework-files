<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Thumbnail Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityThumbnail extends ComFilesModelEntityFile
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'validator' => 'com:files.database.validator.file',
            'data'      => array('crop' => false, 'dimension' => array('width' => 200, 'height' => 150))
        ));

        parent::_initialize($config);
    }

    public function setAdapter()
    {
        $this->_adapter = $this->getContainer()->getAdapter('file', array(
            'path' => $this->getContainer()->fullpath.'/'.($this->folder ? $this->folder.'/' : '').$this->name
        ));

        unset($this->_data['fullpath']);
        unset($this->_data['metadata']);

        return $this;
    }

    public function resize($width)
    {
        $available_extensions = array('jpg', 'jpeg', 'gif', 'png');

        if ($this->isImage()
            && $this->getContainer()->getParameters()->maximum_image_size
            && in_array(strtolower($this->extension), $available_extensions))
        {
            $parameters  = $this->getContainer()->getParameters();
            $size        = $parameters['maximum_image_size'];

            $current_size = @getimagesize($this->fullpath);

            if ($current_size && $current_size[0] > $width || $current_size[1] > $width)
            {
                $thumb = $this->getObject('com:files.model.entity.thumbnail');

                $thumb->dimension = array('width' => $width);
                $thumb->source    = $this;
                $thumb->generate(true);
            }
        }
    }

    public function regenerate()
    {
        $result = false;

        if ($this->_adapter && $this->_adapter->exists())
        {
            $current_size = @getimagesize($this->fullpath);
            $dimension    = $this->getDimension();

            if ($this->crop)
            {
                // Compare dimensions
                if ($current_size && ($current_size[0] != $dimension['width'] || $current_size[1] != $dimension['height'])) {
                    $result = $this->generate(true);
                }
            }
            elseif ($source = $this->source)
            {
                $source_size = @getimagesize($source->fullpath);

                // Compare ratios
                if ($source_size && ($current_size[0] / $current_size[1] != $source_size[0] / $source_size[1])) {
                    $result = $this->generate(true);
                }
            }
        }

        return $result;
    }

    public function generate($in_place = false)
    {
		@ini_set('memory_limit', '256M');

    	if (($source = $this->source) && $this->_canGenerate())
		{
            try
            {
                $imagine = new \Imagine\Gd\Imagine();
                $image   = $imagine->open($source->fullpath);

                $dimension = $this->getDimension();

                if ($dimension['width'] && $dimension['height']) {
                    $size = new \Imagine\Image\Box($dimension['width'], $dimension['height']);
                }
                else
                {
                    $image_size = $image->getSize();
                    $larger     = max($image_size->getWidth(), $image_size->getHeight());
                    $scale      = max($dimension['width'], $dimension['height']);

                    $size       = $image_size->scale(1/($larger/$scale));
                }

                $mode = ($this->crop) ? \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND : \Imagine\Image\ImageInterface::THUMBNAIL_INSET;

                if ($in_place) {
                    return $image->thumbnail($size, $mode)->save($this->fullpath);
                } else {
                    return (string) $image->thumbnail($size, $mode);
                }
			}
			catch (Exception $e) {
				return false;
			}
		}

		return false;
    }

    public function getPropertyRelativePath()
    {
        return $this->getContainer()->relative_path . '/' . $this->path;
    }

    public function save()
    {
        $result = false;

        if ($source = $this->source)
        {
            $str = $source->thumbnail_string ? $source->thumbnail_string : $this->generate();

            if ($str)
            {
                $folder = $this->getContainer()->getAdapter('folder', array(
                    'path' => $this->getContainer()->fullpath.'/'.($this->folder ? $this->folder.'/' : '')
                ));

                if (!$folder->exists()) {
                    $folder->create();
                }

                $this->contents = $str;
                $result         = parent::save();
            }
        }

        return $result;
    }

    public function toArray()
    {
        $data = parent::toArray();

		unset($data['_thumbnail_size']);
		unset($data['source']);

        return $data;
    }

    public function setPropertySource($value)
    {
        if (!$value instanceof ComFilesModelEntityFile) {
            throw new RuntimeException('Wrong class type for source');
        }

        if ($value->isNew()) throw new RuntimeException('Source cannot be a new entity');

        return $value;
    }

    public function setPropertyCrop($value)
    {
        return (bool) $value;
    }

    /**
     * Thumbnail dimension setter.
     *
     * The dimension of a thumbnail consists on a width and height pair.
     *
     * @param  string|array $dimension
     * @throws BadMethodCallException
     * @return $this
     */
    public function setPropertyDimension($value)
    {
        if (!(isset($value['width']) || isset($value['height']))) {
            throw new BadMethodCallException('The provided dimension is invalid');
        }

        return $value;
    }

    /**
     * Thumbnail dimension getter.
     *
     * The dimension of a thumbnail consists on a width and height pair.
     *
     * @throws RuntimeException If no source or its dimension cannot be determined.
     *
     * @return array Associative array containing the thumbnail width and height.
     */
    public function getDimension()
    {
        $dimension = $this->dimension;

        if ($dimension && (!isset($dimension['width']) || !isset($dimension['height'])))
        {
            $source = $this->source;

            if (!($source && ($info = @getimagesize($source->fullpath)))) {
                throw new RuntimeException('Unable to get source size');
            }

            $ratio = $info[0] / $info [1];

            if (isset($dimension['width'])) {
                $dimension['height'] = round($dimension['width'] / $ratio);
            } else {
                $dimension['width'] = round($dimension['height'] * $ratio);
            }
        }

        return $dimension;
    }

    /**
     * Checks if a thumbnail for the current source and provided dimension can be generated given the
     * amount of memory that's available.
     *
     * @return bool True if the thumbnail can be "safely" processed, false otherwise.
     */
    protected function _canGenerate()
    {
        $result = false;

        // Multiplier to take into account memory consumed by the Image Processing Library.
        $tweak_factor  = 6;

        if ($source = $this->source)
        {
            $info = @getimagesize($source->fullpath);

            $channels      = isset($info['channels']) ? $info['channels'] : 4;
            $bits          = isset($info['bits']) ? $info['bits'] : 8;
            $source_memory = ceil($info[0] * $info[1] * $bits * $channels / 8 * $tweak_factor);

            $dimension = $this->getDimension();

            // We assume the same amount of bits and channels as source.
            $thumb_memory = ceil($dimension['width'] * $dimension ['height'] * $bits * $channels / 8 * $tweak_factor);

            //If memory is limited
            $limit = ini_get('memory_limit');
            if ($limit != '-1')
            {
                $limit = self::convertToBytes($limit);
                $available_memory = $limit - memory_get_usage();

                // Leave 16 megs for the rest of the request
                $available_memory -= 16777216;

                if ($source_memory + $thumb_memory < $available_memory) {
                    $result = true;
                }
            }
            else $result = true;
        }

        return $result;
    }

    public static function convertToBytes($value)
    {
        $keys = array('k', 'm', 'g');
        $last_char = strtolower(substr($value, -1));
        $value = (int) $value;

        if (in_array($last_char, $keys)) {
            $value *= pow(1024, array_search($last_char, $keys)+1);
        }

        return $value;
    }
}
