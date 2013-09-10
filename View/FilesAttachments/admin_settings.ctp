<?php
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Settings'), array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'index',
	));
if (!empty($this->request->params['named']['p'])) {
	$this->Html->addCrumb($this->request->params['named']['p']);
}
?>
<h2 class="hidden-desktop"><?php echo __('FilesAttachments Settings') ?></h2>

<?php echo $this->Form->create('FilesAttachment');?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'General'), '#setting-basic');
			//echo $this->Croogo->adminTab(__d('croogo', 'Misc'), '#setting-misc');
		?>
		</ul>

		<div class="tab-content">
			<div id="setting-basic" class="tab-pane">
			<?php
				// maxFileSize
				echo $this->Form->input('maxFileSize.id', array(
					'type' => 'hidden',
					'default' => $defaults['maxFileSize']['id']
				));
				echo $this->Form->input('maxFileSize.value', array(
					'type'  => 'text',
					'value' => $defaults['maxFileSize']['value'],
					'label' => __('Max. size of uploaded file (MB)'),
				));

				// allowedFileTypes
				echo $this->Form->input('allowedFileTypes.id', array(
					'type' => 'hidden',
					'default' => $defaults['allowedFileTypes']['id']
				));
				echo $this->Form->input('allowedFileTypes.value', array(
					'type'  => 'text',
					'value' => $defaults['allowedFileTypes']['value'],
					'label' => __('Coma separated list of allowes extensions'),
				));

				// allowedFileTypesDocument
				echo $this->Form->input('allowedFileTypesDocument.id', array(
					'type' => 'hidden',
					'default' => $defaults['allowedFileTypesDocument']['id']
				));
				echo $this->Form->input('allowedFileTypesDocument.value', array(
					'type'  => 'text',
					'value' => $defaults['allowedFileTypesDocument']['value'],
					'label' => __('Coma separated list of allowes extensions'),
				));

				// thumbnailSizes
				echo $this->Form->input('thumbnailSizes.id', array(
					'type' => 'hidden',
					'default' => $defaults['thumbnailSizes']['id']
				));
				echo $this->Form->input('thumbnailSizes.value', array(
					'type'  => 'textarea',
					'value' => $defaults['thumbnailSizes']['value'],
					'label' => __('Defined alias for thumbnail sizes'),
					'style' => 'height: 90px'
				));

				// remove_settings
				echo $this->Form->input('remove_settings.id', array(
					'type' => 'hidden',
					'default' => $defaults['remove_settings']['id']
				));
				echo $this->Form->input('remove_settings.value', array(
					'label' => __('Remove settings on deactivate?'),
					'options' => array('1' => 'Yes', '0' => 'No'),
					'default' => $defaults['remove_settings']['value']
				));
			?> 
			</div>
		</div>
	</div>
	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Settings')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
