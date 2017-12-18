#!/bin/sh

mkdir ci
cd ci
composer create-project kenjis/codeigniter-composer-installer .
cp application/config/user_agents.php ../backend/admin/application/config/
cp application/config/smileys.php ../backend/admin/application/config/
cp application/config/mimes.php ../backend/admin/application/config/
cp application/config/migration.php ../backend/admin/application/config/
cp application/config/memcached.php ../backend/admin/application/config/
cp application/config/foreign_chars.php ../backend/admin/application/config/
cp application/config/doctypes.php ../backend/admin/application/config/
cp application/config/database.php ../backend/admin/application/config/
cp application/config/constants.php ../backend/admin/application/config/
cp application/config/profiler.php ../backend/admin/application/config/
cp application/config/config.php ../backend/admin/application/config/
rm composer.*
rm .gitignore
rm -rf vendor
rm -rf application