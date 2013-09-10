<?php
App::uses('Component', 'Controller');

/**
 * FilesAttachments Component
 *
 * @category Component
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class FilesAttachmentsComponent extends Component {

/**
 * initialize
 *
 * @param object $controller Controller with components to initialize
 * @return void
 */
	public function initialize(Controller &$controller) {
		$controller->Security->csrfCheck = false;
		$controller->Security->validatePost = false;

		if ($controller->request->params['controller'] == "nodes") {
			switch ($controller->request->params['action']){
				case 'admin_edit':
					if (is_numeric($controller->request->params['pass'][0])) {
						$controller->loadModel('FilesAttachments.FilesAttachment');
						$FilesAttachmentsPhoto = $controller->FilesAttachment->find('all', array(
							'conditions' => array(
								'node_id' => $controller->request->params['pass'][0],
								'status' => true,
								'type' => 'photo'
							),
							'order' => 'id asc',
						));
						if (!empty($FilesAttachmentsPhoto)) {
							$FilesAttachmentsPhoto = Set::extract('{n}.FilesAttachment', $FilesAttachmentsPhoto);
							$FilesAttachmentsPhoto = json_encode($FilesAttachmentsPhoto);
							$controller->set(compact('FilesAttachmentsPhoto'));
						}

						$FilesAttachmentsDocument = $controller->FilesAttachment->find('all', array(
							'conditions' => array(
								'node_id' => $controller->request->params['pass'][0],
								'status' => true,
								'type' => 'document'
							),
							'order' => 'id asc',
						));
						if (!empty($FilesAttachmentsDocument)) {
							$FilesAttachmentsDocument = Set::extract('{n}.FilesAttachment', $FilesAttachmentsDocument);
							$FilesAttachmentsDocument = json_encode($FilesAttachmentsDocument);
							$controller->set(compact('FilesAttachmentsDocument'));
						}
					}
				break;
			}
		}
	}
}
