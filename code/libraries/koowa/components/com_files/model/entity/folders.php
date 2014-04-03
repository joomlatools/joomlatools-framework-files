<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Folders Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityFolders extends ComFilesModelEntityNodes
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'identity_key' => 'path'
        ));

        parent::_initialize($config);
    }

	/**
     * Adds the rows as a hierachical tree of nodes.
     *
     * {@inheritdoc}
     */
    public function create(array $properties = array(), $status = null)
    {
        $entity = parent::create($properties, $status);

        $hierarchy = $entity->hierarchy;

        if(!empty($hierarchy) && is_array($entity->hierarchy))
        {
            // We are gonna add it as a child of another node
            $this->remove($entity);

            $nodes   = $this;
            $node    = null;

            foreach($hierarchy as $key => $parent)
            {
                $path = implode('/', array_slice($hierarchy, 0, $key+1));

                if ($node) {
                    $nodes = $node->getChildren();
                }

                $node = $nodes->find($path);
            }

            if (!$node) {
                throw new RuntimeException('Parent folder not found in the tree');
            }

            $node->insertChild($entity);
        }

        $entity->removeProperty('hierarchy');

        return $entity;
    }
}
