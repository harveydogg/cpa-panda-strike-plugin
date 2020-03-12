# cpa-panda-strike-plugin
A wordpress plugin for the Cessna Pilots Association.


### Updating the plugin on the CPA website

1. Make code edits
1. Manually increment the version number in the comments at the top of `cpa-panda-strike-plugin.php` (this is where wp gets the plugin version number. yes, it's insane)
1. Commit and push up the changes
1. Package up the files by executing `make` in the root of this repository
1. [Browse to the plugin page][1] and `Remove` the plugin
1. [Browse to the upload plugin page][2] and upload the zip file that we created earlier
1. Flip the `Active` toggle to activate the plugin
1. Verify that the website has not caught :fire:


[1]:https://wordpress.com/plugins/cpa-panda-strike-plugin/cessnapilotsassociation.com
[2]:https://wordpress.com/plugins/upload/cessnapilotsassociation.com
