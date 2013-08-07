<?php
/**
 * @package     Nooku_Components
 * @subpackage  Files
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Translator Helper Class
 * 
 * Translates JavaScript language keys
 *
 * @author      Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package     Nooku_Components
 * @subpackage  Files
 */
class ComFilesTemplateHelperTranslator extends KTemplateHelperAbstract
{
    public function javascript($config = array())
    {
        $config = new KConfig($config);
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
        
        $keys = KConfig::unbox($config->keys);
        
        $map = array();
        foreach ($keys as $key) {
            $map[$key] = $this->translate($key);
        }
        
        ob_start();
        ?>
        <script>
        if (typeof Files === 'undefined') {
            Files = {};
        }
        (function() {
            var keys = <?php echo json_encode($map); ?>;
            Files._ = function(key) {
                if (typeof keys[key] !== 'undefined') {
                    return keys[key];
                }

                return key;
            };
        })();
        </script>
        <?php 
        
        $html = ob_get_clean();
        
        return $html;
    }
}
