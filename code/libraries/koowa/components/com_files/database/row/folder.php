<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa-files for the canonical source repository
 */

/**
 * Folder Database Row
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDatabaseRowFolder extends ComFilesDatabaseRowNode
{
	/**
	 * Nodes object or identifier (com://APP/COMPONENT.rowset.NAME)
	 *
	 * @var string|object
	 */
	protected $_children = null;

	/**
	 * Node object or identifier (com://APP/COMPONENT.rowset.NAME)
	 *
	 * @var string|object
	 */
	protected $_parent   = null;

	public function save()
	{
		$context = $this->getContext();
		$context->result = false;

		$is_new = $this->isNew();

		if ($this->getCommandChain()->run('before.save', $context, false) !== false)
		{
			if ($this->isNew()) {
				$context->result = $this->_adapter->create();
			}

			$this->getCommandChain()->run('after.save', $context);
		}

		if ($context->result === false)
		{
			$this->setStatus(KDatabase::STATUS_FAILED);
			$this->setStatusMessage($context->getError());
		}
		else $this->setStatus($is_new ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED);

		return $context->result;
	}

	public function __get($column)
	{
		if ($column == 'children' && !isset($this->_data['children'])) {
			$this->_data['children'] = $this->getObject('com://admin/files.database.rowset.folders');
		}

		return parent::__get($column);
	}

	public function toArray()
	{
		$data = parent::toArray();

		if ($this->hasChildren()) {
			$data['children'] = $this->getChildren()->toArray();
		}

		return $data;
	}

	public function getData($modified = false)
	{
		$result = parent::getData($modified);

		if (isset($result['children']) && $result['children'] instanceof KDatabaseRowsetInterface) {
			$result['children'] = $result['children']->getData();
		}

		return $result;
	}

	public function insertChild(KDatabaseRowInterface $node)
	{
		//Track the parent
		$node->setParent($this);

		//Insert the row in the rowset
		$this->getChildren()->insert($node);

		return $this;
	}

	public function hasChildren()
	{
		return (boolean) count($this->_children);
	}

	/**
	 * Get the children rowset
	 *
	 * @return	object
	 */
	public function getChildren()
	{
		if(!($this->_children instanceof KDatabaseRowsetInterface))
		{
			$identifier         = clone $this->getIdentifier();
			$identifier->path   = array('database', 'rowset');
			$identifier->name   = KStringInflector::pluralize($this->getIdentifier()->name);

			//The row default options
			$options  = array(
				'identity_column' => $this->getIdentityColumn()
			);

			$this->_children = $this->getObject($identifier, $options);
		}

		return $this->_children;
	}

	/**
	 * Get the parent node
	 *
	 * @return	object
	 */
	public function getParent()
	{
		return $this->_parent;
	}

	/**
	 * Set the parent node
	 *
	 * @return ComArticlesDatabaseRowNode
	 */
	public function setParent( $node )
	{
		$this->_parent = $node;
		return $this;
	}
}
