<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Folders Database Rowset
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityFolders extends ComFilesModelEntityNodes
{
    /**
     * Returns if an iterator can be created for the current entry.
     *
     * @return	boolean
     */
	public function hasChildren()
	{
		return current($this->_data)->hasChildren();
	}

	/**
     * Returns an iterator for the current entry.
     *
     * @return	RecursiveIterator
     */
	public function getChildren()
	{
		return $this->current()->getChildren();
	}

	/**
     * Adds the rows as an hierachical tree of nodes.
     *
     * This function requires each row to contain a an enumerated 'path' array containing the node
     * id's from root to the node. If no path exists or the path is empty the row will be added to
     * the root node.
     *
	 * @param  array  	$list An associative array of row data to be inserted.
	 * @param  boolean	$new  If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
	 * @return  KDatabaseRowsetAbstract
	 * @see __construct
     */
	public function addData(array $list, $new = true)
    {
    	foreach($list as $k => $row)
		{
			$hierarchy = !empty($row['hierarchy']) ? $row['hierarchy'] : false;
			unset($row['hierarchy']);

		    //Create a row prototype and clone it this is faster then instantiating a new row
			$instance = $this->getRow()
							->setData($row)
							->setStatus($new ? NULL : KDatabase::STATUS_LOADED);

        	if($hierarchy)
        	{
        		$nodes   = $this;
				$node    = null;
				$parents = $hierarchy;

				foreach($parents as $parent)
       			{
       				if($node) {
						$nodes = $node->getChildren();
					}

       				$node = $nodes->find($parent);
				}

				$node->insertChild($instance);
        	}
            else $this->insert($instance);
		}

		return $this;
    }
}
