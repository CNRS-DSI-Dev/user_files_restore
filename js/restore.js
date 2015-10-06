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

    if ($('#freeCreate input[type=button]')) {
        $('#freeCreate input[type=button]').on('click', function(event) {
            // ask to confirm
            event.preventDefault();
            OCdialogs.confirm(
                t('user_files_restore', 'Are you sure to CONFIRM this global restoration request? All your files will be overwritten by this restoration.'),
                t('user_files_migrate', 'Confirm global migration request'),
                confirmGlobalRestorationRequest,
                true
            );
        });

        $('#freeCreate p.header img').tipsy({html: true });
        $('#running p.header img').tipsy({html: true });
        $('#done span.errorback img').tipsy({html: true });
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
        var type = $(this).attr('data-type');
        restoreFile(file, revision, type, false);
    });

    $('#todo').on('click', 'span.cancel', function() {
        var id = $(this).attr('data-id');
        cancelRequest(id);
    });
});

function confirmGlobalRestorationRequest(response) {
    if (response == false) {
        return;
    }

    var version = $('#freeCreate option:selected').val();
    restoreFile('/', version, 'dir', true);
}

function restoreFile(file, revision, type, freeCreate) {
    $.ajax({
        type: 'POST',
        url: OC.generateUrl('apps/user_files_restore/api/1.0/request'),
        dataType: 'json',
        data: {file: file, version: revision, filetype: type},
        async: false,
        success: function(response) {
            if (response.status === 'error') {
                if (response.data.msg != '') {
                    OC.Notification.show(response.data.msg);
                }
                else {
                    OC.Notification.show( t('user_files_restore', 'Failed to create Restore request for {file}.', {file:file}) );
                }
                setTimeout(OC.Notification.hide, 7000);
            }
            else if (response.status === 'collision_error') {
                OCdialogs
                    .message(
                        '____________________________________',
                        t('user_files_restore', 'Restore request'),
                        'info',
                        OCdialogs.OK_BUTTON,
                        function(ok) {
                            if (ok) {
                                // TODO: VIRER LE CALLBACK;
                                console.log('hop');
                            }
                        },
                        true)
                    .then(function() {
                        var contentDiv = $('div.oc-dialog-content p');
                        var toKeep = JSON.parse(response.data.toKeep);
                        var toCancel = JSON.parse(response.data.toCancel);

                        if (toCancel.length > 0) {
                            var msg = "<b>" + t('user_files_restore', "Your request collided with previous request(s).") + "</b></br></br>";
                            msg = msg + t('user_files_restore', "These precedent requests will be automatically cancelled: ") + "</br>";
                            msg = msg + toCancel.join('</br>') + "</br>";

                            contentDiv.html(msg);
                        }
                    });
            }
            else {
                OC.Notification.show( t('user_files_restore', 'Request successfully created') );
                setTimeout(OC.Notification.hide, 7000);

                $('#dropdown').hide('blind', function() {
                    $('#dropdown').closest('tr').find('.modified:first').html(relative_modified_date(revision));
                    $('#dropdown').remove();
                    $('tr').removeClass('mouseOver');
                });

                if (freeCreate) {
                    location.reload();

                    // TODO : create a API to get the #todo requests list, then loop on the list
                    // var html = "<p id=\"" + response.data.id + "\">";
                    // html += response.data.file;
                    // html += "<span>(custom ; </span>";
                    // html += "<span>" + response.data.version + ")</span>";
                    // html += "<span class=\"cancel\" data-id=\"" + response.data.id + "\" data-version=\"" + response.data.version + "\">";
                    // html += t('user_files_restore', 'Cancel') + "</span></p>";

                    // $('#todo').append(html);
                }
            }
        }
    });
}

function createRestoreDropdown(filename, files, fileList) {
    var start = 0;
    var fileEl;

    var html = '<div id="dropdown" class="drop drop-versions" data-file="'+escapeHTML(files)+'">';
    html += '<div id="private">';
    html += '<ul id="available_versions">';
    html += '</ul>';
    html += '</div>';

    var filetype = '';

    if (filename) {
        fileEl = fileList.findFileEl(filename);
        filetype = fileEl.attr('data-type');
        fileEl.addClass('mouseOver');
        $(html).appendTo(fileEl.find('td.filename'));
    } else {
        $(html).appendTo($('thead .share'));
    }

    var versions = [];
    $.ajax({
        type: 'GET',
        url: OC.generateUrl('apps/user_files_restore/api/1.0/versions'),
        dataType: 'json',
        async: false,
        success: function(response) {
            if (response.status === 'error') {
                OC.Notification.show( t('user_files_restore', 'Failed to get versions.') );
                setTimeout(function() {
                    OC.Notification.hide();
                }, 10000);
            } else {
                versions = JSON.parse(response.data.versions);

                $.each(versions, function(idx, version) {
                    addRestoreVersion(version);
                });
            }
        }
    });

    function addRestoreVersion(version) {
        var download='<span class="versionName">';
        download+=version + " day(s) ago</span>";

        var revert='<span class="restoreVersion"';
        revert+=' id="' + version + '"';
        if (filetype != 0) {
            revert+=' data-type="' + filetype + '"';
        }
        revert+='>';
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

function cancelRequest(idRequest) {
    $.ajax({
        type: 'POST',
        url: OC.generateUrl('apps/user_files_restore/api/1.0/cancel'),
        dataType: 'json',
        data: {id: idRequest},
        async: false,
        success: function(response) {
            if (response.status === 'error') {
                OC.Notification.show( t('user_files_restore', 'Failed to cancel Restore request.') );
                $('p#'+idRequest).addClass('error');
                setTimeout(function() {
                    $('p#'+idRequest).removeClass('error');
                    OC.Notification.hide();
                }, 10000);
            } else {
                $('p#'+idRequest).remove();
            }
        }
    });
}
