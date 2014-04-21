<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Translator Helper Class
 * 
 * Translates JavaScript language keys
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperTranslator extends KTemplateHelperAbstract
{
    public function javascript($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'keys' => array(
                'Bytes', 'KB', 'MB', 'GB', 'TB', 'PB',
                'An error occurred during request',
            	'You are deleting {item}. Are you sure?',
                'You are deleting {items}. Are you sure?',
            	'{count} files and folders',
            	'{count} folders',
            	'{count} files',
                'All Files',
                'An error occurred with status code: ',
                'An error occurred: ',
                'Unknown error',
                'Uploaded successfully!',
                'Select files from your computer',
                'Choose File'
            )
        ));
        
        $keys = KObjectConfig::unbox($config->keys);
        
        $map = array();
        foreach ($keys as $key) {
            $map[$key] = addslashes($this->translate($key));
        }
        
        ob_start();
        ?>
        <script>
        Koowa.translator.loadTranslations(<?php echo json_encode($map); ?>);
        </script>
        <?php 
        
        $html = ob_get_clean();
        
        return $html;
    }
}
