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
class ComFilesControllerBehaviorAttachable extends KControllerBehaviorAbstract
{
    protected function _beforeDelete(KControllerContextInterface $context)
    {
        if ($attachments = $context->getRequest()->getData()->attachments)
        {
            $entities = $this->getModel()->fetch();

            $controller = $this->_getController();

            foreach ($entities as $entity)
            {
                foreach ($attachments as $attachment)
                {
                    $request = $controller->getRequest();

                    $request->setQuery(array(
                        'row'   => $entity->id,
                        'table' => $entities->getTable()->getBase(),
                        'name'  => $attachment
                    ));

                    // Reset the state.
                    $controller->getModel()->setState($request->getQuery()->toArray());

                    $controller->delete();
                }
            }

            $context->getResponse()->setStatus(KHttpResponse::NO_CONTENT);

            return false;
        }
    }

    protected function _beforeEdit(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($attachments = $request->getData()->attachments)
        {
            $controller = $this->_getController();

            foreach ($this->getModel()->fetch() as $entity)
            {
                foreach ($attachments as $attachment) {
                    $controller->add(array(
                        'table' => $entity->getTable()->getBase(),
                        'row'   => $entity->id,
                        'name'  => $attachment
                    ));
                }
            }

            $context->getResponse()->setStatus(KHttpResponse::NO_CONTENT);

            return false;
        }
    }

    protected function _getController()
    {
        $parts = $this->getMixer()->getIdentifier()->toArray();

        $parts['path'] = array('controller');
        $parts['name'] = 'attachment';

        return $this->getObject($parts);
    }
}

