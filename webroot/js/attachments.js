var AttachmentsClass = function(nameSelector) {

/**
 * onUpdate method
 *
 * @param boolean updateJson Default true
 * @access private
 * @return void
 */
	var onUpdate = function(updateJson) {
		var ul = $('#faList'+nameSelector);
		if (updateJson == undefined) {
			updateJson = true;
		}
	
		ul.sortable({
			handle: '.fa-move',
			stop: function() {
				toJson();
			}
		});

		var liVisible = $('#faList'+nameSelector+' li:visible');
		if (liVisible.find('.fa-main-photo:checked').length == 0) {
			liVisible.find('.fa-main-photo:first').attr('checked', 'checked');
		}

		liVisible.find('a.fancybox').fancybox({
			openEffect: 'none',
			closeEffect: 'none'
		});

		ul.find('.fa-remove').bind('click', function() {
			if (confirm(Croogo.FilesAttachments.i18n['messageConfirmDelete'])) {
				$(this).parent().parent().fadeOut(function() {
					showCount();
					onUpdate();
				});
			}
		});
		ul.find('.fa-description').bind('change', function() {
			toJson();
		});
		ul.find('.fa-main-photo').bind('click', function() {
			toJson();
		});

		if (updateJson === true) {
			toJson();
		}
	};

/**
 * toJson method
 *
 * @access private
 * @return void
 */
	var toJson = function() {
		var list = [];
		$('#faList'+nameSelector+' li:visible').each(function() {
			var data = {
				'filename': $(this).data('filename'),
				'real_filename': $(this).data('real_filename'),
				'type': nameSelector.toLowerCase()
			};
			if ($(this).find('textarea.fa-description').val()) {
				data.description = $(this).find('textarea.fa-description').val();
			}
			if ($(this).find('.fa-main-photo').length > 0) {
				data.main_photo = $(this).find('.fa-main-photo').is(':checked') ? 1 : 0;
			}
			list.push(data);
		});
		if (list.length > 0) {
			var json = JSON.encode(list);
			$('#JsonAttachments'+nameSelector).val(json);
		} else {
			$('#JsonAttachments'+nameSelector).val('');
		}
	};

/**
 * Show count attachments
 *
 * @param int count
 * @access private
 */
	var showCount = function(count) {
		if (count == undefined) {
			count = $('.qq-upload-list li:visible').length;
		}
		$("form#NodeAdminEditForm .nav-tabs li a[href=#node-attachments],form#NodeAdminAddForm .nav-tabs li a[href=#node-attachments]").html(Croogo.FilesAttachments.i18n['tabName'] +' ('+ count +')');
	};

/**
 * getTemplateThumb
 *
 * @param string template
 * @access private
 * @return string
 */
	var getTemplateThumb = function(template, options) {
		for (key in options) {
			var rexp = new RegExp("\{"+key+"\}", "g");
			template = template.replace(rexp, options[key]);
		}
		return template;
	};

/**
 * merge method
 *
 * @param object merge
 * @param object fields
 * @param object res
 * @access private
 * @return object
 */
	var addFieldsArray = function(merge, fields, res) {
		if (typeof(fields) == 'object') {
			for (key in fields) {
				if (typeof(res[fields[key]]) != 'undefined') {
					if (res[fields[key]] == null) {
						res[fields[key]] = '';
					}
					merge[fields[key]] = res[fields[key]];
				}
			}
		}
		return merge;
	}

/**
 * build method
 *
 * @param string template
 * @param object options
 * @param object additionalFields
 * @access public
 * @return void
 */
	this.build = function(template, options, additionalFields) {
		new qq.FileUploader({
			element: $('#faUpload'+nameSelector)[0],
			action: '/admin/FilesAttachments/upload/',
			allowedExtensions: options.allowedFileTypes,
			sizeLimit: options.maxFileSize,
			dragDrop: false,
			multiple: options.multiple,
			template: '<div class="qq-uploader">' + 
					'<div class="qq-upload-button">'+Croogo.FilesAttachments.i18n['Upload']+'</div>' +
					'<ul class="qq-upload-list" id="faList'+nameSelector+'"></ul>' +
					'</div>',
			onLoad: function() {
				var jsonString = $('#JsonAttachments'+nameSelector).val();
				if (jsonString != '') {
					var list = JSON.decode(jsonString), ul = $('#faList'+nameSelector), li = '';
					for (var key = 0; key <= list.length-1; key++) {
						var opt = {
							filename: list[key].filename,
							real_filename: list[key].real_filename,
							id: key
						};
						opt = addFieldsArray(opt, additionalFields, list[key]);
						var li = getTemplateThumb(template, opt);
						ul.append(li);

						if (list[key]['main_photo'] != undefined && list[key]['main_photo']) {
							$('#faMainPhoto'+key).attr('checked', 'checked');
						}
					}
					showCount($('.qq-upload-list li').length);
					onUpdate(false);
				}
			},
			onComplete: function(id, fileName, responseJSON) {
				if (responseJSON.success) {
					var opt = {
						filename: responseJSON.filename,
						real_filename: responseJSON.real_filename,
						id: id
					};

					opt = addFieldsArray(opt, additionalFields, responseJSON);

					var li = getTemplateThumb(template, opt);
					var ul = $('#faList'+nameSelector);
					ul.find('li.qq-upload-success').remove();
					ul.append(li);
					showCount();
					onUpdate();
				}
			}
		});
	}
};
