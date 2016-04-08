<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * File Controller
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesControllerFile extends ComFilesControllerAbstract
{
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->addCommandCallback('before.add' , '_setFile');
        $this->addCommandCallback('before.edit', '_setFile');
	}
	
	protected function _initialize(KObjectConfig $config)
	{
		$config->append(array(
			'behaviors' => array('chunkable', 'thumbnailable'),
            'toolbars' => array('com:files.controller.toolbar.file')
		));

		parent::_initialize($config);
	}

	protected function _setFile(KControllerContextInterface $context)
	{
		if (empty($context->request->data->file) && $context->request->files->has('file'))
		{
			$context->request->data->file = $context->request->files->file['tmp_name'];
			if (empty($context->request->data->name)) {
				$context->request->data->name = $context->request->files->file['name'];
			}
		}
	}

    public function getRequest()
    {
        $request = parent::getRequest();

        // This is used to circumvent the URL size exceeding 2k bytes problem for file counts in uploader
        if ($request->query->view === 'files' && $request->data->has('name')) {
            $request->query->name = $request->data->name;
        }

        return $request;
    }

    protected function _actionRender(KControllerContextInterface $context)
    {
        $model  = $this->getModel();
        $result = null;

        if ($this->getRequest()->getFormat() === 'html')
        {
            // Serve file
            if ($model->getState()->isUnique())
            {
                $file = $this->getModel()->fetch();

                try
                {
                    $this->getResponse()
                        ->attachTransport('stream')
                        ->setContent($file->fullpath, $file->mimetype);
                }
                catch (InvalidArgumentException $e) {
                    throw new KControllerExceptionResourceNotFound('File not found');
                }
            }
            else
            {
                $query     = $this->getRequest()->query;
                $container = $this->getModel()->getContainer();

                // Note: PHP converts dots to underscores in cookie names
                $cookie = json_decode($this->getObject('request')->cookies['com_files_container_'.$container->slug.'_state'], true);

                if (strpos($query->layout, 'compact') === false && is_array($cookie))
                {
                    // Check if the folder exists, folder shouldn't exist in query for cookie to be used
                    if (isset($cookie['folder']))
                    {
                        $adapter = $container->getAdapter('folder');
                        $adapter->setPath($container->fullpath . '/' . $cookie['folder']);
                        // Unset folder cookie if path does not exists.
                        if (!$adapter->exists()) {
                            unset($cookie['folder']);
                        }
                    }

                    foreach ($cookie as $key => $value)
                    {
                        if (!$query->has($key)) {
                            $query->$key = $value;
                        }
                    }

                    $model->getState()->setValues($query->toArray());
                }

                $result = parent::_actionRender($context);
            }
        }
        else $result = parent::_actionRender($context);

        return $result;
    }
}
