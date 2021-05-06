# Install

## Clone the project via git
```shell
git clone git@github.com:NJG-CORP/test-task-files.git
```

## Install dependencies via composer
```shell
composer install
```

# Test

## Create additional files
Add a few new files if needed in
```shell
/public/files/
```

## Run the command
```shell
php bin/console file {filename} {?directory}
```

### Examples
Default directory
```shell
php bin/console file A.txt
```
Different internal directory
```shell
php bin/console file A.txt public/another
```
Absolute path
```shell
php bin/console file A.txt /var/www/data/YOURPATH
```