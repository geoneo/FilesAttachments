<?php
/**
 * Public routes - Photo
 */
CroogoRouter::connect(
	'/fa/photoByFilename/:filename/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'photoByFilename', 'admin' => false),
	array('pass' => array('filename'))
);
CroogoRouter::connect(
	'/fa/photoByNode/:node/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'photoByNode', 'admin' => false),
	array('pass' => array('node'))
);
CroogoRouter::connect(
	'/fa/getPhotos/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'getPhotos', 'admin' => false)
);

/**
 * Public routes - Document
 */
CroogoRouter::connect(
	'/fa/documentByFilename/:filename/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'documentByFilename', 'admin' => false),
	array('pass' => array('filename'))
);
CroogoRouter::connect(
	'/fa/documentByNode/:node/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'documentByNode', 'admin' => false),
	array('pass' => array('node'))
);
CroogoRouter::connect(
	'/fa/getDocuments/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'getDocuments', 'admin' => false)
);

/**
 * Private routes - Admin
 */
CroogoRouter::connect(
	'/admin/FilesAttachments/settings',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'settings', 'admin' => true)
);
CroogoRouter::connect(
	'/admin/FilesAttachments/upload/*',
	array('plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'upload', 'admin' => true)
);


