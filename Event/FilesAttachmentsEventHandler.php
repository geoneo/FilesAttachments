<?php
/**
 * FilesAttachments Event Handler
 *
 * PHP version 5
 *
 * @category Event
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class FilesAttachmentsEventHandler extends Object implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
		return array(
			'Controller.Nodes.afterAdd' => array(
				'callable' => 'onNodeAfterAdd'
			),
			'Controller.Nodes.afterEdit' => array(
				'callable' => 'onNodeAfterEdit'
			),
		);
	}

/**
 * onNodeAfterAdd
 *
 * @param object $event
 * @access public
 * @return void
 */
	public function onNodeAfterAdd($event) {
		$Controller = $event->subject();
		$Controller->loadModel('FilesAttachments.FilesAttachment');
		if (!empty($event->data['data']['Json'])) {
			if (!empty($event->data['data']['Json']['attachments_photo'])) {
				$this->__saveData($Controller, $event->data['data']['Json']['attachments_photo']);
			}
			if (!empty($event->data['data']['Json']['attachments_document'])) {
				$this->__saveData($Controller, $event->data['data']['Json']['attachments_document']);
			}
		}
	}

/**
 * onNodeAfterEdit
 *
 * @param object $event
 * @access public
 * @return void
 */
	public function onNodeAfterEdit($event) {
		$Controller = $event->subject();
		$Controller->loadModel('FilesAttachments.FilesAttachment');
		if (!empty($Controller->request->data)) {
			if (empty($Controller->request->data['Json']['attachments_photo'])) {
				$Controller->FilesAttachment->deleteAll(array('node_id' => $Controller->Node->id, 'type' => 'photo'), false, true);
			} else {
				$Controller->FilesAttachment->deleteAll(array('node_id' => $Controller->Node->id, 'type' => 'photo'), false, false);
				$this->__saveData($Controller, $Controller->request->data['Json']['attachments_photo']);
			}

			if (empty($Controller->request->data['Json']['attachments_document'])) {
				$Controller->FilesAttachment->deleteAll(array('node_id' => $Controller->Node->id, 'type' => 'document'), false, true);
			} else {
				$Controller->FilesAttachment->deleteAll(array('node_id' => $Controller->Node->id, 'type' => 'document'), false, false);
				$this->__saveData($Controller, $Controller->request->data['Json']['attachments_document']);
			}
		}
	}

/**
 * __saveData method
 *
 * @param object $controller
 * @param array $filesAttachmentsData
 * @access private
 * @return boolean|array
 */
	private function __saveData($controller, $filesAttachmentsData)
	{
		$filesAttachmentsData = @json_decode($filesAttachmentsData, true);
		if (is_array($filesAttachmentsData)) {
			$data = array();
			foreach ($filesAttachmentsData as $val) {
				$val['node_id'] = $controller->Node->id;
				if (!empty($val['description'])) {
					$val['description'] = htmlspecialchars($val['description']);
				}
				$data[] = array('FilesAttachment' => $val);
			}
			return $controller->FilesAttachment->saveAll($data);
		}
		return false;
	}

}
