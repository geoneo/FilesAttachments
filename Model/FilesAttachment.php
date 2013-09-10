<?php
App::uses('AppModel', 'Model');
/**
 * FilesAttachments App Model
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class FilesAttachment extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'files_attachments';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'filename';

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	/*public $belongsTo = array(
		'Node' => array(
			'className' => 'Nodes.Node',
			'foreignKey' => 'node_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);*/

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * Called before every deletion operation.
 *
 * @param boolean $cascade If true records that depend on this record will also be deleted
 * @return boolean True if the operation should continue, false if it should abort
 */
	public function beforeDelete($cascade) {
		$deleteAttach = $this->findById($this->id);
		if (file_exists(FA_UPLOAD_PATH . $deleteAttach['FilesAttachment']['filename'])) {
			if (unlink(FA_UPLOAD_PATH . $deleteAttach['FilesAttachment']['filename'])) {
				return true;
			} else {
				return false;
			}
		}
	}

/**
 * afterSave callback
 *
 * @param boolean $created True if this save created a new record
 * @return boolean
 */
	public function afterSave($created)
	{
		if ($created) {
			if (isset($this->data[$this->alias]['filename'])) {
				$filename = $this->data[$this->alias]['filename'];
				if (file_exists(FA_TMP . $filename)) {
					@copy(FA_TMP . $filename, FA_UPLOAD_PATH . $filename);
					@unlink(FA_TMP . $filename);
				}
			}
		}
	}

/**
 * formats the settings gotten from the Settings model to a usable format for
 * this plugin (this plugin saves some settings in json, and this returns the
 * values in an array, if thats the case)
 *
 * @param type $settings
 * @return type
 */
	public function getSettings($settings) {
		$retSet = false;
		if (is_array($settings)) {
			foreach ($settings as $setting) {
				$cleanedKey = explode('.', $setting['Setting']['key']);
				$retSet[$cleanedKey[1]]['id'] = $setting['Setting']['id'];
				$retset = array();
				if (strpos($cleanedKey[1], "__json") !== false) {
					$settingJson = json_decode($setting['Setting']['value'], true);
					foreach ($settingJson as $settingValue) {
						if (strpos($settingValue, ":")) {
							$microsetting = explode(":", $settingValue);
							$retset[$microsetting[0]] = $microsetting[1];
						}
						if (count($retset) > 0) {
							$retSet[$cleanedKey[1]]['values'] = $retset;
						} else {
							$retSet[$cleanedKey[1]]['values'] = $settingJson;
						}
					}
				}
				$retSet[$cleanedKey[1]]['value'] = $setting['Setting']['value'];
			}
		}
		return $retSet;
	}

}
