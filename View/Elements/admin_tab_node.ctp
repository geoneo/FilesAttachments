<?php
/**
 * Element tab_node
 *
 */

// Add Css
$this->Html->css('FilesAttachments.tab.css', null, array('inline' => false));
$this->Html->css('FilesAttachments.fileuploader.css', null, array('inline' => false));
$this->Html->css('/FilesAttachments/js/fancybox/source/jquery.fancybox.css', null, array('inline' => false));

// Add Js
$this->Html->script('/FilesAttachments/js/fileuploader/client/fileuploader.js', false);
$this->Html->script('/FilesAttachments/js/JSON.min.js', false);
$this->Html->script('/FilesAttachments/js/jquery-ui/minified/jquery.ui.core.min.js', false);
$this->Html->script('/FilesAttachments/js/jquery-ui/minified/jquery.ui.widget.min.js', false);
$this->Html->script('/FilesAttachments/js/jquery-ui/minified/jquery.ui.mouse.min.js', false);
$this->Html->script('/FilesAttachments/js/jquery-ui/minified/jquery.ui.position.min.js', false);
$this->Html->script('/FilesAttachments/js/jquery-ui/minified/jquery.ui.sortable.min.js', false);
$this->Html->script('/FilesAttachments/js/fancybox/source/jquery.fancybox.pack.js', false);
$this->Html->script('/FilesAttachments/js/attachments.js', false);

// These variables are used in the scriptBlock
$settings = Configure::read('FilesAttachments');
if (!empty($settings['allowedFileTypes'])) {
	$settings['allowedFileTypes'] = explode(',', $settings['allowedFileTypes']);
}
if (!empty($settings['allowedFileTypesDocument'])) {
	$settings['allowedFileTypesDocument'] = explode(',', $settings['allowedFileTypesDocument']);
}
$settings['maxFileSize'] = $settings['maxFileSize'] * 1024 * 1024;
$settingsJson = json_encode($settings);
$i18n = array(
	'Upload' => __('Upload'),
	'Description' => __('Description'),
	'messageConfirmDelete' => __('Are you sure you want to delete?'),
	'Main photo' => __('Main photo'),
	'tabName' => __d('croogo', 'Attachments')
);
$i18nJson = json_encode($i18n);

// scriptBlock (Photo Upload)
$this->Html->scriptBlock(
<<<JS
$(function() {
	Croogo.FilesAttachments = {};
	Croogo.FilesAttachments.i18n = $i18nJson;
	var settings = $settingsJson;

	var attachPhoto = new AttachmentsClass('Photo');
	attachPhoto.build(
		'<li data-real_filename="{real_filename}" data-filename="{filename}" class="clearfix"><div class="fa-column1"><a href="/fa/photoByFilename/{filename}/" class="fancybox" rel="gallery1" target="_blank" title=""><img src="/fa/photoByFilename/{filename}/dimension:thumb/crop:1" alt="" /></a></div> <div class="fa-column2"><textarea name="fa[description]" id="faDescription{id}" class="fa-description" placeholder="{$i18n['Description']}">{description}</textarea><div class="fa-div-main-photo"><input type="radio" name="fa[mainphoto]" id="faMainPhoto{id}" class="fa-main-photo" value="1" /><label for="faMainPhoto{id}">{$i18n['Main photo']}</label></div></div> <div class="fa-column3"><a href="javascript:void(0)" class="fa-remove"><span class="icon-remove"></span></a> <a href="javascript:void(0)" class="fa-move"><span class="icon-move"></span></a></div></li>',
		{
			allowedFileTypes: settings.allowedFileTypes,
			maxFileSize: settings.maxFileSize,
			multiple: true
		},
		['description']
	);

	var attachDoc = new AttachmentsClass('Document');
	attachDoc.build(
		'<li data-real_filename="{real_filename}" data-filename="{filename}" class="clearfix"><div class="fa-column1"><a href="/fa/documentByFilename/{filename}/download:0" target="_blank"><span>{filename}</span></a></div> <div class="fa-column2"><a href="javascript:void(0)" class="fa-remove"><span class="icon-remove"></span></a></div></li>',
		{
			allowedFileTypes: settings.allowedFileTypesDocument,
			maxFileSize: settings.maxFileSize,
			multiple: true
		}
	);

});
JS
, array('inline' => false));
?>
<br/>
<div class="input clearfix">
	<label for=""><?=__('Images')?></label>
	<div id="faUploadPhoto"></div>
	<div style="display:block">
		<?php
			$options = array();
			$options['type'] = 'hidden';
			if (!empty($this->data['Json']['attachments_photo'])) {
				$options['value'] = $this->data['Json']['attachments_photo'];
			} else {
				if (!empty($FilesAttachmentsPhoto) && is_string($FilesAttachmentsPhoto)) {
					$options['value'] = $FilesAttachmentsPhoto;
				}
			}
			echo $this->Form->input('Json.attachments_photo', $options);
		?>
	</div>
</div>
<div class="input clearfix">
	<label for=""><?=__('Documents')?></label>
	<div id="faUploadDocument"></div>
	<div style="display:block">
		<?php
			$options = array();
			$options['type'] = 'hidden';
			if (!empty($this->data['Json']['attachments_document'])) {
				$options['value'] = $this->data['Json']['attachments_document'];
			} else {
				if (!empty($FilesAttachmentsDocument) && is_string($FilesAttachmentsDocument)) {
					$options['value'] = $FilesAttachmentsDocument;
				}
			}
			echo $this->Form->input('Json.attachments_document', $options);
		?>
	</div>
</div>
