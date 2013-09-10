<?php
App::uses('CakeSchema', 'Model');
App::uses('ConnectionManager', 'Model');

/**
 * FilesAttachments Activation
 *
 * Activation class for FilesAttachments plugin.
 * This is optional, and is required only if you want to perform tasks when your plugin is activated/deactivated.
 *
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class FilesAttachmentsActivation {

/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
	public function beforeActivation(&$controller) {
		return true;
	}

/**
 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
	public function onActivation(&$controller) {

		$tableName = 'files_attachments';
		$pluginName = 'FilesAttachments';
		$db = ConnectionManager::getDataSource('default');
		$tables = $db->listSources();

		// Revisar si existe la tabla, si no usar el schema que se proporciona para generarla
		if (!in_array(strtolower($tableName), $tables)) {
			$schema = & new CakeSchema(array(
				'name' => $pluginName,
				'path' => APP . 'Plugin' . DS . $pluginName . DS . 'Config' . DS . 'schema',
			));
			$schema = $schema->load();
			foreach ($schema->tables as $table => $fields) {
				$create = $db->createSchema($schema, $table);
				try {
					$db->execute($create);
				} catch (PDOException $e) {
					die(__('Could not create table: %s', $e->getMessage()));
				}
			}
		}
		// ACL: set ACOs with permissions
		$controller->Croogo->addAco('FilesAttachments');
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments');
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/photoByFilename', array('registered', 'public'));
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/photoByNode', array('registered', 'public'));
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/getPhotos', array('registered', 'public'));
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/getDocuments', array('registered', 'public'));
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/admin_upload');
		$controller->Croogo->addAco('FilesAttachments/FilesAttachments/admin_settings');

		$thumbnail = array(
			'thumb:95,95',
			'gallery:150,150',
			'normal:0,0',
		);
		$thumbnail = json_encode($thumbnail);

		$controller->Setting->write('FilesAttachments.remove_settings', '0', array(
			'editable' => 1, 'description' => 'Remove settings on deactivate'
		));
		$controller->Setting->write('FilesAttachments.maxFileSize', '5', array(
			'editable' => 1, 'description' => __('Max. size of uploaded file (MB)', true)
		));
		$controller->Setting->write('FilesAttachments.allowedFileTypes', 'jpg,gif,png', array(
			'editable' => 1, 'description' => __('Coma separated list of allowes extensions (empty = all files)', true)
		));
		$controller->Setting->write('FilesAttachments.thumbnailSizes', $thumbnail, array(
			'editable' => 1, 'description' => __('Defined alias for thumbnail sizes')
		));
		$controller->Setting->write('FilesAttachments.allowedFileTypesDocument', 'pdf,doc,docx,odt,xls,ods,txt', array(
			'editable' => 1, 'description' => __('Defined alias for thumbnail sizes')
		));
	}

/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
	public function beforeDeactivation(&$controller) {
		return true;
	}

/**
 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
	public function onDeactivation(&$controller) {
		// ACL: remove ACOs with permissions
		$controller->Croogo->removeAco('FilesAttachments');

		// Remove Allowed MIME types
		if (Configure::read('FilesAttachments.remove_settings') == '1') {
			$controller->Setting->deleteKey('FilesAttachments.allowed_mime__json');
			$controller->Setting->deleteKey('FilesAttachments.thumbnail_sizes__json');
		}
	}

}
