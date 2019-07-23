## About this project


## Usage

First you need to [install composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar) 
for your setup.

- After this you can clone the project with ___
- Install the site with composer install
- Install the theme with radix documentation...
- Enable the theme
- Create the homepage
- Add keys for gmaps, algoria, captcha (and make sure these are not in config/sync!)

## TODO
Remove modules: 
- date_recur
- geolocation?
- phayes/geophp drupal/geofield drupal/geofield_map
- GMapss needs credit card info
- paragraphs
- hide node title event date
- webform helpersunknown

Enable modules
- bigpipe

- Create GMaps webform location select OR just a simple address field that simple_gmaps uses to lookup
- Create newsletter process
- Leaflet to replace GMAPs?
- URL alias on events
- delete gmfood_general_node_access?

Remove fields:
- event/start_date

Update Dev modules
- entity_reference_revisions
- fullcalendar


## How to edit CSS / template files / etc for non-Drupal users
- Editing CSS
- Editing HTML
- Editing layout
- Editing fields and forms
- Editing webforms
- Exporting config
- Commiting to git
- .htaccess notes?

## Drupal template directories

The Drupal template used makes use of some default directories

* Drupal is be installed in the `web`-directory.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`
* Theme (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`
* Profiles (packages of type `drupal-profile`) will be placed in `web/profiles/contrib/`
* Creates default writable versions of `settings.php` and `services.yml`.
* Creates `web/sites/default/files`-directory.
* Latest version of drush is installed locally for use at `vendor/bin/drush`.
* Latest version of DrupalConsole is installed locally for use at `vendor/bin/drupal`.
* Creates environment variables based on your .env file. See [.env.example](.env.example).
* Drupal config is stored in config/sync folder

## Updating Drupal Core

Follow the steps below to update your core files.

1. Run `composer update drupal/core webflo/drupal-core-require-dev "symfony/*" --with-dependencies` to update Drupal Core and its dependencies.
1. Run `git diff` to determine if any of the scaffolding files have changed. 
   Review the files for any changes and restore any customizations to 
  `.htaccess` or `robots.txt`.
1. Commit everything all together in a single commit, so `web` will remain in
   sync with the `core` when checking out branches or running `git bisect`.
1. In the event that there are non-trivial conflicts in step 2, you may wish 
   to perform these steps on a branch, and use `git merge` to combine the 
   updated core files with your customized files. This facilitates the use 
   of a [three-way merge tool such as kdiff3](http://www.gitshah.com/2010/12/how-to-setup-kdiff-as-diff-tool-for-git.html). This setup is not necessary if your changes are simple; 
   keeping all of your modifications at the beginning or end of the file is a 
   good strategy to keep merges easy.
