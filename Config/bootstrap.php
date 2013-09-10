<?php

Croogo::hookRoutes('FilesAttachments');
Croogo::hookComponent('Nodes', 'FilesAttachments.FilesAttachments');

Croogo::hookAdminTab('Nodes/admin_add', 'Attachments', 'FilesAttachments.admin_tab_node');
Croogo::hookAdminTab('Nodes/admin_edit', 'Attachments', 'FilesAttachments.admin_tab_node');

CroogoNav::add('settings.children.files_attachments',array(
	'title' => __('Files Attachments'),
	'url' => array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'settings'),
	'access' => array('admin')
));

define('FA_UPLOAD_PATH', WWW_ROOT . 'uploads' . DS . 'nodes' . DS);
define('FA_TMP', TMP . 'filesuploads' . DS);
