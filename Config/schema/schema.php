<?php 
class FilesAttachmentsSchema extends CakeSchema {

/**
 * Before callback
 *
 * @param array $event
 * @return void
 */
	public function before($event = array()) {

	}

/**
 * After callback
 *
 * @param array $event
 * @return void
 */
	public function after($event = array()) {

	}

 /**
  * CakePHP schema
  *
  * @var array
  */
	public $files_attachments = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'key' => 'primary'),
		'node_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'key' => 'index'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'length' => 50),
		'filename' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'length' => 255),
		'real_filename' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'length' => 255),
		'description' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'link' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'length' => 255),
		'main_photo' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'status' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'node_id' => array('column' => 'node_id', 'unique' => 0),
		),
		'tableParameters' => array(
			'charset' => 'utf8',
			'collate' => 'utf8_unicode_ci',
			'engine' => 'MyISAM'
		)
	);

}
