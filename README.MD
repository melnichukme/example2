## Install project

To install on linux, use the **make** utility

> $ apt-get install make

Run the command at the root of the project. It will take a few minutes.
> $ make install

Create a local environment file for the project

> $ touch ./app/.env.local

Set debug mode and database connection in **.env.local**

```text
APP_DEBUG=true
DATABASE_URL="mysql://rhymes-crm:secret@mysql:3307/rhymes-crm?serverVersion=mariadb-10.6.7&charset=utf8mb4"
```

If everything is fine, you will see the login form at the link:

[http://localhost](http://localhost)

🎉

## Make commands

**Main**
- **make up** - start docker services
- **make down** - stop docker services
- **make restart** - restart docker services
- **make build** - build docker services
- **make env-create** - create docker services configuration file (before build)
- **make generate-ssl** - generate the SSL keys

**Project**
- **make install** - first install project
- **make update-dev** - update only !dev project
- **make git-pull** - update project files from vcs

**Composer**
- **make composer-install** - install packages for dev
- **make composer-install-no-dev** - install packages for production (without dev packages)
- **make composer-update** - update all packages

**Frontend**
- **make npm-watch** - dev watch mode
- **make npm-install-all** - install packages
- **make npm-install pkg="package_name"** - install specific package
- **make npm-remove pkg="package_name"** - remove specific package
- **make npm-build** - compile assets to bundle build version

**Migrations \ DB**
- **make migrate** - execute a migration to a specified version or the latest available version
- **make migrate-diff** - generate a migration by comparing your current database to your mapping information
- **make migrate-status** - view the status of a set of migrations
- **make fixtures-load** - fixtures are used to load a "fake" set of data into a db

**Other**
- **make terminal** - login to php-fpm container (bash)
- **make cache-clear** - clear application cache
