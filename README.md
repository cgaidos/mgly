# moowgly

Install
====

Target environment
----

Apache: 2.4+
- modules: 
 . rewrite_module.
 . mod_auth_openidc : https://github.com/pingidentity/mod_auth_openidc/releases
PHP: 7.0+
- modules: php_curl.
MySQL: 5.6+

Prerequisite
----

Install and add to path following components :
* [Composer] [1]
* [PHP-CS-Fixer] [2]
* [Bower] [3]
    
Install dependencies 
----

````
# Composer
composer update
# Bower
bower install
# Gulp
npm install -g gulp
npm install
gulp
````
If you have Ubuntu 14.10 running as a VMware guest on your Windows 7 host, the command is ````npm install gulp --no-bin-links```` 
````--no-bin-links```` tells npm to not create any symbolic links. There isn't a way (to my knowledge) of translating symlinks to a windows share.

If there is this error : /usr/bin/env: node: No such file or directory
we need to do this : sudo ln -s "$(which nodejs)" /usr/local/bin/node

In _production environment_, following command should be preferred : 
````
composer update --optimize-autoloader
````

Coding standards fixer
----
Following commands allow to fix PHP coding standards as defined in the [PSR-1] [4] and [PSR-2] [5] documents and can be execute from project folder :

````
php-cs-fixer fix lib/src
php-cs-fixer fix silo/src
````


Apache configuration
====

Change url & database setting according to your values :

````
Alias /moowgly "E:\localweb\moowgly\pilote\public" 

Alias /ws-moowgly "E:\localweb\moowgly\silo\public" 

````

SQL data creation script
====

1. Creation of a user "moowglyUser" with password *moowgly#$@, with GRANT privileges on database "moowgly".

2. Creation of tables in mysql:

-- --------------------------------------------------------

--
-- Structure of the table `user`
--
````
DROP TABLE IF EXISTS user;
CREATE TABLE IF NOT EXISTS `user` (
  id int(8) NOT NULL AUTO_INCREMENT,
  email varchar(50) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
````

[1]: https://getcomposer.org/           "Composer"
[2]: http://cs.sensiolabs.org/          "PHP-CS-Fixer"
[3]:http://bower.io/
[4]: http://www.php-fig.org/psr/psr-1/  "PSR-1"
[5]: http://www.php-fig.org/psr/psr-2/  "PSR-2"

DynamoDB configuration and data
====
