<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

// Include autoloader for Imagine
if (!class_exists('\Imagine\Gd\Imagine') && is_file(dirname(dirname(__DIR__)).'/vendor/autoload.php')) {
    require_once dirname(dirname(__DIR__)).'/vendor/autoload.php';
}

return array(
);