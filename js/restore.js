/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

/* global scanFiles, escapeHTML, formatDate */
$(document).ready(function(){

	if ($('#isPublic').val()){
		// no versions actions in public mode
		// beware of https://github.com/owncloud/core/issues/4545
		// as enabling this might hang Chrome
		return;
	}

	if (OCA.Files) {
		// Add versions button to 'files/index.php'
		OCA.Files.fileActions.register(
			'all',
			'Restore',
			OC.PERMISSION_READ,
			function() {
				// Specify icon for hitory button
				return OC.imagePath('user_files_restore','restore3');
			},
			function(filename, context) {
				// Action to perform when clicked
				if (scanFiles.scanning){return;}//workaround to prevent additional http request block scanning feedback

				var file = context.dir.replace(/(?!<=\/)$|\/$/, '/' + filename);

				var createDropDown = true;
				// Check if drop down is already visible for a different file
				if (($('#dropdown').length > 0) ) {
					if ( $('#dropdown').hasClass('drop-versions') && file == $('#dropdown').data('file')) {
						createDropDown = false;
					}
					$('#dropdown').remove();
					$('tr').removeClass('mouseOver');
				}

				if(createDropDown === true) {
					createRestoreDropdown(filename, file, context.fileList);
				}
			},
			t('user_files_restore', 'Restore')
		);
	}

	$(document).on("click", 'span[class="restoreVersion"]', function() {
		var revision = $(this).attr('id');
		var file = $(this).attr('value');
		restoreFile(file, revision);
	});

});

function restoreFile(file, revision) {
console.log(file);
console.log(revision);
	$.ajax({
		type: 'POST',
		url: OC.generateUrl('apps/user_files_restore/api/1.0/request'),
		dataType: 'json',
		data: {file: file, version: revision},
		async: false,
		success: function(response) {
			if (response.status === 'error') {
				OC.Notification.show( t('user_files_restore', 'Failed to create Restore request for {file}.', {file:file}) );
			} else {
				$('#dropdown').hide('blind', function() {
					$('#dropdown').closest('tr').find('.modified:first').html(relative_modified_date(revision));
					$('#dropdown').remove();
					$('tr').removeClass('mouseOver');
				});
			}
		}
	});

}

function createRestoreDropdown(filename, files, fileList) {

	var start = 0;
	var fileEl;

	var html = '<div id="dropdown" class="drop drop-versions" data-file="'+escapeHTML(files)+'">';
	html += '<div id="private">';
	// html += '<ul id="found_versions">';
	html += '<ul id="available_versions">';
	html += '</ul>';
	html += '</div>';
	// html += '<input type="button" value="'+ t('files_versions', 'More versions...') + '" name="show-more-versions" id="show-more-versions" style="display: none;" />';

	if (filename) {
		fileEl = fileList.findFileEl(filename);
		fileEl.addClass('mouseOver');
		$(html).appendTo(fileEl.find('td.filename'));
	} else {
		$(html).appendTo($('thead .share'));
	}

	addRestoreVersion(1);
	addRestoreVersion(15);
	addRestoreVersion(30);

	function addRestoreVersion(version) {
		var download='<span class="versionName">';
		download+=version + " day(s) ago</span>";

		var revert='<span class="restoreVersion"';
		revert+=' id="' + version + '">';
		revert+='<img';
		revert+=' src="' + OC.imagePath('user_files_restore', 'restore3') + '"';
		revert+=' name="restoreVersion"';
		revert+='/>'+t('user_files_restore', 'Restore')+'</span>';

		var restoreVersion=$('<li/>');
		restoreVersion.attr('value', version);
		restoreVersion.html(download + revert);
		// add file here for proper name escaping
		restoreVersion.find('span.restoreVersion').attr('value', files);

		restoreVersion.appendTo('#available_versions');
	}

	$('#dropdown').show('blind');
}

$(this).click(
	function(event) {
	if ($('#dropdown').has(event.target).length === 0 && $('#dropdown').hasClass('drop-versions')) {
		$('#dropdown').hide('blind', function() {
			$('#dropdown').remove();
			$('tr').removeClass('mouseOver');
		});
	}


	}
);
