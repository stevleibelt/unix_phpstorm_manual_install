Description
===========

Simple bash script collection to easy up installing or upgrading existing phpstorm on your unig machine.

Usage
=====

* a group called "developer" should exist on your system
* the users who should work with phpstorm should be member of the group "developer"
* download newest version from [jetbrains.com](http://www.jetbrains.com/phpstorm/download/download_thanks.jsp?os=linux)
* cd into this repostiory
* execute following code in your shell
    bash updatePhpStorm.sh /path/to/new/version/PhpStorm-x.y.z.tar.gz
* for first installation, execute following code in your shell
    sudo ln -s /opt/phpstorm/bin/phpstorm.sh /usr/bin/phpstorm

Todo
====

* collect todos from existing scripts
* move steps into separate directory
* provide alias file you simple can source in your shell

Version history
===============

* 2014-03-19 - initial commit (its working on my machine)
