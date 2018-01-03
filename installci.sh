#!/bin/sh

mkdir ci
cd ci
composer create-project kenjis/codeigniter-composer-installer .
cp application/config/user_agents.php ../backend/application/config/
cp application/config/smileys.php ../backend/application/config/
cp application/config/mimes.php ../backend/application/config/
cp application/config/migration.php ../backend/application/config/
cp application/config/memcached.php ../backend/application/config/
cp application/config/foreign_chars.php ../backend/application/config/
cp application/config/doctypes.php ../backend/application/config/
cp application/config/database.php ../backend/application/config/
#cp application/config/constants.php ../backend/application/config/
cp application/config/profiler.php ../backend/application/config/
cp application/config/config.php ../backend/application/config/
rm composer.*
rm .gitignore
rm README.md
rm -rf vendor
rm -rf application
rm -rf bin