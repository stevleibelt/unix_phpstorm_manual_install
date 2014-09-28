# Description

The script automates the installation or updating of an existing phpstorm installation on a unix machine (tested on Debian Linux 6 and 7).

The script itself is very verbose. If something happens, it should support you.
Furthermore, the script is creating a backup of your existing installation and outputs the backup path.

# Usage 

First of all, go to the [download page](http://www.jetbrains.com/phpstorm/download/) and download the newest version.


```
# simple install or update
php phpstorm.php path/to/linux/php/version.tar.gz 

# install or update and change group
#   good if you created a "developer" group on your system
php phpstorm.php path/to/linux/php/version.tar.gz your_group_name
```

# Version history

* [1.0.1](https://github.com/stevleibelt/unix_phpstorm_manual_install/tree/1.0.0) - not released yet
    * fixed broken link in readme
* [1.0.0](https://github.com/stevleibelt/unix_phpstorm_manual_install/tree/1.0.0) - released at 28.09.2014
    * initial commit
