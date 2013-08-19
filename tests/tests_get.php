<?php

/*
 * Root file
 */
$result = KObjectManager::get('com://admin/files.controller.file')
	->container('files-files')
	->name('joomla_logo_black.jpg')
	->read()->toArray();

var_dump('Root file', $result);

/*
 * Nested file
 */
$result = KObjectManager::get('com://admin/files.controller.file')
	->container('files-files')
	->folder('banners')
	->name('osmbanner1.png')
	->read()->toArray();

var_dump('Nested file', $result);

/*
 * Root folder
 */
$result = KObjectManager::get('com://admin/files.controller.folder')
	->container('files-files')
	->name('banners')
	->read()->toArray();

var_dump('Root folder', $result);

/*
 * Nested folder
 */
$result = KObjectManager::get('com://admin/files.controller.folder')
	->container('files-files')
	->folder('stories')
	->name('food')
	->read()->toArray();

var_dump('Nested folder', $result);

/*
 * Root files
 */
$result = KObjectManager::get('com://admin/files.controller.file')
	->container('files-files')
	->limit(5)
	->browse()->toArray();

var_dump('Root files', $result);

/*
 * Nested files
 */
$result = KObjectManager::get('com://admin/files.controller.file')
	->container('files-files')
	->folder('stories')
	->limit(5)
	->browse()->toArray();

var_dump('Nested files', $result);

/*
 * Root folders
 */
$result = KObjectManager::get('com://admin/files.controller.folder')
	->container('files-files')
	->limit(5)
	->browse()->toArray();

var_dump('Root folders', $result);

/*
 * Nested folders
 */
$result = KObjectManager::get('com://admin/files.controller.folder')
	->container('files-files')
	->folder('stories')
	->limit(5)
	->browse()->toArray();

var_dump('Nested folders', $result);

/*
 * Root nodes
 */
$result = KObjectManager::get('com://admin/files.controller.node')
	->container('files-files')
	->limit(5)
	->browse()->toArray();

var_dump('Root nodes', $result);

/*
 * Nested nodes
 */
$result = KObjectManager::get('com://admin/files.controller.node')
	->container('files-files')
	->folder('stories')
	->limit(5)
	->browse()->toArray();

var_dump('Nested nodes', $result);
