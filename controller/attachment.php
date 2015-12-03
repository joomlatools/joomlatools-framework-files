<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Attachment Controller
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Koowa\Component\Files
 */
class ComFilesControllerAttachment extends ComKoowaControllerModel
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('behaviors' => array('relatable')));

        $aliases = array(
            'com:files.model.attachments'                => array(
                'path' => array('model'),
                'name' => 'attachments'
            ),
            'com:files.model.attachments_relations' => array(
                'path' => array('model'),
                'name' => 'attachments_relations'
            ),
            'com:files.controller.permission.attachment' => array(
                'path' => array('controller', 'permission'),
                'name' => 'attachment'
            ),
            'com:files.controller.behavior.relatable' => array(
                'path' => array('controller', 'behavior'),
                'name' => 'relatable'
            )
        );

        $manager = $this->getObject('manager');

        foreach ($aliases as $identifier => $alias)
        {
            $alias = array_merge($this->getIdentifier()->toArray(), $alias);

            if (!$manager->getClass($alias, false)) {
                $manager->registerAlias($identifier, $alias);
            }
        }

        parent::_initialize($config);
    }

    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->getRequest()->isAjax() && $context->result->getStatus !== KDatabase::STATUS_FAILED) {
            $context->getResponse()->setStatus(KHttpResponse::NO_CONTENT);
        }
    }

    public function setView($view)
    {
        $view = parent::setView($view);

        if ($view instanceof KObjectIdentifierInterface && $view->getPackage() !== 'files')
        {
            $manager = $this->getObject('manager');

            if (!$manager->getClass($view, false))
            {
                $identifier = $view->toArray();
                $identifier['package'] = 'files';
                unset($identifier['domain']);

                $manager->registerAlias($identifier, $view);
            }
        }

        return $view;
    }
}