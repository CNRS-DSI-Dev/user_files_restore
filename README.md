# User Files Restore

Dedicated My CoRe platform app, allowing the user to request files restoration from Tivoli backup. Restoration itself is processed by external scripts (see https://github.com/CNRS-DSI-Dev/mycore_backup_restore_user_files) 

## Install

As usual, just put the user_files_restore directory inside the "apps" directory in your owncloud instance.

When you will activate the app, a app configuration key `auto_create_user` will be created in table oc_appconfig valued with `[1, 6, 15]`.
This config key set the allowed "versions" to backup from: it's the number of days from today to the backup that will be restored.

You may change these values but it has to be accordingly to the Tivoli Backup Manager configration.

## Use

The user will be offered a new action on file's hovering : "Restore" that allows to request a restoration on a specific file or directory.

There's a special page that lists all pending, running or closed requests.

All the requests informations are stored on a specific table `oc_user_files_restore`.

## Special cases

On this "Restoration requests" page, you may request a restoration on a deleted file or directory (as it does not appear on files page nay more). Just type the filepath on the text field, select a "version", then `Send` the request.

Note that this feature allows to request a restoration on the whole directory. Just put "/" in the text field.

## Deleting old closed requests informations

A command line script is provided, allowing to delete the old entries in the table `oc_userfiles_restore`.

You need to set a key in `config.php`, to set the number of days you want to keep closed requests in database.

```php
 'ufr_clean_delay' => 30,
```

Without this key in `config.php`, the delay will be set to `7`.

To clean the `oc_user_files_restore` table, use

```shell
cd [owncloud]/
./occ user_files_restore:clean
```

## Contributing

This app is developed for an internal deployement of ownCloud at CNRS (French National Center for Scientific Research).

If you want to be informed about this ownCloud project at CNRS, please contact david.rousse@dsi.cnrs.fr, gilian.gambini@dsi.cnrs.fr or marc.dexet@dsi.cnrs.fr

## License and authors

|                      |                                          |
|:---------------------|:-----------------------------------------|
| **Author:**          | Patrick Paysant (<ppaysant@linagora.com>)
| **Copyright:**       | Copyright (c) 2015 CNRS DSI
| **License:**         | AGPL v3, see the COPYING file.
