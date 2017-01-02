/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2016 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

(function(OC, OCA, Util) {
    // will use Handlebars to create the final html (see template and render functions)
    // Handlebars : http://handlebarsjs.com/
    var TEMPLATE =
        'TODO / TRANSLATE You may choose to restore an older version of this file: ' +
        '<ul id="available_versions"></ul>' +
        '<div class="clear-float"></div>';

    var VERSION_TEMPLATE =
        '<li><span class="versionName">{{version}}</span>' +
        '<span class="restoreVersion" data-id="{{versionId}}" data-type="{{fileType}}">' +
        '<img src="' + OC.imagePath('user_files_restore', 'restore3') + '" name="restoreVersion" />' +
        t('user_files_restore', 'Restore') +
        '</span>' +
        '</li>';

    /**
     * @member of OCA.UserFilesRestore
     */
    var UserFilesRestoreTabView = OCA.Files.DetailTabView.extend({
        id: 'userFilesRestoreTabView',
        className: 'tab userFilesRestoreTabView',

        _template: null,

        // declare to which events you want to react and how
        events: {
            'click .restoreVersion': '_onClickRestoreVersion',
        },

        // just initialize TabView, probably not needed...
        initialize: function() {
            OCA.Files.DetailTabView.prototype.initialize.apply(this, arguments);
        },

        // gives the tab's label
        getLabel: function() {
            return t('user_files_restore', 'Restore');
        },

        // declared as response to an event (see events declaration, before)
        _onClickRestoreVersion: function(ev) {
            var self = this;
            var $target = $(ev.target);

            var revision = $target.attr('data-id');

            ev.preventDefault();

            var fileInfo = this.getFileInfo();
            var sep = '/';
            if ('/' == fileInfo.attributes['path']) {
                sep = '';
            }
            var file = fileInfo.attributes['path'] + sep + fileInfo.attributes['name'];
            var type = fileInfo.attributes['type'];

            // ask confirmation
            OCdialogs
                .confirm(
                    t('user_files_restore', '____________________________________________'),
                    t('user_files_restore', 'Confirm your restoration request'),
                    function(ok) {
                        if (ok) {
                            Util._restoreFile(file, revision, type, false);
                        }
                    },
                    true)
                .then(function() {
                    var contentDiv = $('div.oc-dialog-content p');

                    var msg = t('user_files_restore', "Are you sure to restore to D-{version} version ?", {version:revision}) + "</br></br>";
                    msg = msg + t('user_files_restore', "Once restored, the present version will be overridden.") + "</br>";

                    contentDiv.html(msg);
                });

        },

        _drawVersion: function() {
            var self = this;

            var url = OC.generateUrl('apps/user_files_restore/api/1.0/versions');
            $.get(url, function(data) {
                if (data.status == 'success') {
                    var versions = JSON.parse(data.data.versions);
                    _.each(versions, function(versionId) {
                        this._versionTemplate = Handlebars.compile(VERSION_TEMPLATE);
                        var $li = $(this._versionTemplate({
                            version: n('user_files_restore', "%n day ago", "%n days ago", versionId),
                            versionId: versionId,
                            fileType: self.getFileInfo().attributes['type']
                        }))
                        self.$container.append($li);
                    });
                }
            });
        },

        // util function to precompile the handlebar template (which is a function, not direct html, see render function)
        template: function(data) {
            if (!this._template) {
                this._template = Handlebars.compile(TEMPLATE);
            }

            return this._template(data);
        },

        // render the tab
        render: function() {
            this.$el.html(this.template({
                user: t('user_files_restore', 'world')
            }));

            this.$container = this.$el.find('ul#available_versions');

            this._drawVersion();

            this.delegateEvents();
            return this;
        },
    });

    OCA.UserFilesRestore.UserFilesRestoreTabView = UserFilesRestoreTabView;
})(OC, OCA, OCA.UserFilesRestore.Util);

