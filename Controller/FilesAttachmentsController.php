<?php

App::uses('AppController', 'Controller');

/**
 * FilesAttachments Controller
 *
 * @category Controller
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class FilesAttachmentsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'FilesAttachments';

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'FilesAttachments.FilesAttachments',
		'FilesAttachments.UtilImage'
	);
	
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Setting', 'FilesAttachments.FilesAttachment');

/**
 * Defaults settings
 *
 * @var array
 * @access public
 */
	public $defaults = array();

/**
 * pluginPrefix
 *
 * @var array
 * @access public
 */
	public $pluginPrefix = "FilesAttachments";

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		// This gets all the settings and sets them for views in the variable
		// 'default'. This will be available inside FilesAttachments views, and
		// $this->defaults can be used in this controller.
		$this->loadModel('Settings.Setting');
		$settings = $this->Setting->find('all', array('conditions' => array('Setting.key LIKE' => $this->pluginPrefix . '.%')));
		$this->defaults = $this->FilesAttachment->getSettings($settings);
		$this->set('defaults', $this->defaults);
		$this->Security->csrfCheck = false;
	}

/**
 * photoByFilename method
 *
 * @param string $filename
 * @access public
 */
	public function photoByFilename($filename = '') {
		$this->autoRender = false;

		$options = array_merge(array(
			'crop' => 1,
			'dimension' => '',
		), $this->request->named);

		$src = $this->__getSource($filename);
		if (!isset($src)) {
			return false;
		}

		$dimension = $this->__getDimension($options['dimension']);

		$this->UtilImage->openImage($src);
		$this->UtilImage->resize(array(
			'width' => $dimension['width'],
			'height' => $dimension['height'],
			'crop' => $options['crop']==1
		));
	}

/**
 * documentByFilename method
 *
 * @param string $filename
 * @access public
 */
	public function documentByFilename($filename = '') {
		$this->autoRender = false;

		$src = $this->__getSource($filename);
		if (!isset($src)) {
			return false;
		}

		$options = array_merge(array(
			'download' => 1
		), $this->request->named);

		$mime = mime_content_type($src);
		header("Content-type: $mime");

		if ($options['download'] == 1) {
			header("Content-Disposition: attachment; filename=$filename");
		}

		echo file_get_contents($src);
	}

/**
 * photoByNode method
 *
 * @param int $node  Node id
 * @access public
 */
	public function photoByNode($node = 0) {
		$this->autoRender = false;

		$options = array_merge(array(
			'crop' => 1,
			'dimension' => '',
		), $this->request->named);

		$fa = $this->FilesAttachment->find('first', array(
			'conditions' => array(
				'node_id' => $node,
				'status' => true,
				'main_photo' => true,
				'type' => 'photo'
			),
			'recursive' => -1
		));
		if (empty($fa)) {
			return false;
		}

		$src = $this->__getSource($fa['FilesAttachment']['filename']);
		if (!isset($src)) {
			return false;
		}

		$dimension = $this->__getDimension($options['dimension']);

		$this->UtilImage->openImage($src);
		$this->UtilImage->resize(array(
			'width' => $dimension['width'],
			'height' => $dimension['height'],
			'crop' => $options['crop']==1
		));
	}

/**
 * documentByNode method
 *
 * @param int $node  Node id
 * @access public
 */
	public function documentByNode($node = 0) {
		$this->autoRender = false;

		$fa = $this->FilesAttachment->find('first', array(
			'conditions' => array(
				'node_id' => $node,
				'status' => true,
				'type' => 'document'
			),
			'recursive' => -1
		));
		if (empty($fa)) {
			return false;
		}

		$src = $this->__getSource($fa['FilesAttachment']['filename']);
		if (!isset($src)) {
			return false;
		}

		$options = array_merge(array(
			'download' => 1
		), $this->request->named);

		$mime = mime_content_type($src);
		header("Content-type: $mime");

		if ($options['download'] == 1) {
			header("Content-Disposition: attachment; filename=$filename");
		}

		echo file_get_contents($src);
	}

/**
 * getPhotos method
 *
 * @param
 * @access public
 * @return array
 */
	public function getPhotos()
	{
		$this->autoRender = false;
		if (empty($this->request->named['node_id'])) {
			return false;
		}
		$nodeId = $this->request->named['node_id'];
		$query = array(
			'conditions' => array('node_id' => $nodeId, 'status' => true, 'type' => 'photo'),
			'order' => 'id asc',
		);
		if (isset($this->request->named['main_photo'])) {
			$query['conditions']['main_photo'] = $this->request->named['main_photo'];
		}
		$photos = $this->FilesAttachment->find('all', $query);
		$photos = Set::extract('{n}.FilesAttachment', $photos);
		return $photos;
	}
/**
 * getDocuments method
 *
 * @param
 * @access public
 * @return array
 */
	public function getDocuments()
	{
		$this->autoRender = false;
		if (empty($this->request->named['node_id'])) {
			return false;
		}
		$nodeId = $this->request->named['node_id'];
		$documents = $this->FilesAttachment->find('all', array(
			'conditions' => array('node_id' => $nodeId, 'status' => true, 'type' => 'document'),
			'order' => 'id asc',
		));
		$documents = Set::extract('{n}.FilesAttachment', $documents);
		return $documents;
	}

/**
 * upload method
 * This function is via AJAX call for the fileupload script javascript
 *
 * @access public
 * @return array JSON
 */
	public function admin_upload() {
		set_time_limit(240);
		ini_set('memory_limit', '128M');

		$filename = $_GET['qqfile'];
		$fn = substr($filename, 0, strrpos($filename, '.'));
		$ext = explode('.', $filename);
		$ext = strtolower(end($ext));
		$filename = strtolower(Inflector::slug($fn)) .'.'. $ext;
		$response = array('real_filename' => $filename, 'description' => '');

		// Renames the original file with a unique name
		$response['filename'] = md5(uniqid(rand(), true)) .'.'. $ext;
		if (!empty($_GET['qqfile'])) {
			$_GET['qqfile'] = $response['filename'];
		} else {
			$_FILES['qqfile']['name'] = $response['filename'];
		}

		$ext1 = explode(',', Configure::read('FilesAttachments.allowedFileTypes'));
		$ext2 = explode(',', Configure::read('FilesAttachments.allowedFileTypesDocument'));
		$allowedExtensions = array_merge($ext1, $ext2);
		$sizeLimit = Configure::read('FilesAttachments.maxFileSize') * 1024 * 1024;
		App::import('Vendor', 'FilesAttachments.file_uploader');
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

		$result = $uploader->handleUpload(FA_TMP);
		$response += $result;

		$this->set('response', $response);
		$this->render('FilesAttachments/admin_upload', 'json/admin');
	}

/**
 * admin_settings
 *
 * FilesAttachments settings
 * @access public
 * @return void
 */
	public function admin_settings() {
		$this->set('title_for_layout', __('', true));
		if (!empty($this->data)) {
			$settings = &ClassRegistry::init('Setting');
			foreach ($this->data as $key => $setting) {
				$settings->id = $setting['id'];
				if (strpos($key, "__json") !== false) {
					$setting['value'] = preg_split('/\r\n|[\r\n]/', $setting['value']);
					$setting['value'] = json_encode($setting['value']);
				}
				$settings->saveField('value', $setting['value']);
			}
			$this->redirect(array('action' => 'settings'));
			$this->Session->setFlash(__('Plugin settings have been saved', true));
		}
		$this->set('defaults', $this->defaults);
	}

/**
 * getSource method
 *
 * @param string $filename
 * @access private
 * @return string|boolean
 */
	private function __getSource($filename)
	{
		if (file_exists(FA_UPLOAD_PATH . $filename)) {
			$src = FA_UPLOAD_PATH . $filename;
		} else {
			if (file_exists(FA_TMP . $filename)) {
				$src = FA_TMP . $filename;
			} else {
				return false;
			}
		}
		return $src;
	}

/**
 * getDimension method
 *
 * @param string $dimension
 * @access private
 * @return array
 */
	private function __getDimension($dimension)
	{
		$width = $height = null;
		preg_match('/^(?<width>\d*),?(?<height>\d*)?$/', $dimension, $matches);
		if (empty($matches)) {
			$thumbnail = json_decode(Configure::read('FilesAttachments.thumbnailSizes'), true);
			foreach ($thumbnail as $val) {
				$p = explode(':', $val);
				if ($p[0] == $dimension) {
					$d = explode(',', trim($p[1]));
					if (!empty($d[0])) {
						$width = $d[0];
					}
					if (!empty($d[1])) {
						$height = $d[1];
					}
					break;
				}
			}
		} else {
			$width = $matches['width'];
			if (!empty($matches['height'])) {
				$height = $matches['height'];
			}
		}
		return compact('width', 'height');
	}
}
