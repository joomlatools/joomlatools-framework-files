<?php

class ComFilesConfigState extends KConfigState
{
 	public function __set($name, $value)
    {
        if ($name === 'container' && is_string($value)) {
            $value = KService::get('com://admin/files.model.containers')->slug($value)->getItem();

	        if (!is_object($value) || $value->isNew()) {
	            throw new KModelException('Invalid container');
	        }
        }
        
    	parent::__set($name, $value);
  	}
}