<?php
/**
 * Element gallery
 *
 * @param int $nodeId
 * @param string $dimension
 * @param int $crop
 */
if (empty($nodeId)) {
	if (!empty($node['Node']['id'])) {
		$nodeId = $node['Node']['id'];
	} else {
		return;
	}
}
if (empty($dimension)) {
	$dimension = 'gallery';
}
if (!isset($crop)) {
	$crop = 1;
}


$this->Html->css('/FilesAttachments/js/fancybox/source/jquery.fancybox.css', null, array('inline' => false));
$this->Html->script('/FilesAttachments/js/fancybox/source/jquery.fancybox.pack.js', false);

$photos = $this->requestAction(
	array('admin' => false, 'plugin' => 'FilesAttachments', 'controller' => 'FilesAttachments', 'action' => 'getPhotos'),
	array('named' => array(
		'node_id' => $nodeId,
	))
);

$this->Html->scriptBlock(
<<<JS
$(function() {
	$('ul.fa-gallery .fancybox').fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
});
JS
, array('inline' => false));
?>
<?php if (!empty($photos)):?>
<ul class="fa-gallery">
<?php foreach ($photos as $photo):?>
		<li><?=$this->Html->link(
				$this->Html->image(array(
					'plugin' => 'FilesAttachments',
					'controller' => 'FilesAttachments',
					'action' => 'photoByFilename',
					'dimension' => $dimension,
					'crop' => $crop,
					'admin' => false,
					'filename' => $photo['filename'],
				), array('alt' => $photo['real_filename'])),
				array(
					'plugin' => 'FilesAttachments',
					'controller' => 'FilesAttachments',
					'action' => 'photoByFilename',
					'filename' => $photo['filename']
				),
				array(
					'escape' => false,
					'class' => 'fancybox',
					'rel' => 'gallery1',
					'title' => $photo['description']
				)
			)?></li>
<?php endforeach?>
</ul>
<?php endif?>
