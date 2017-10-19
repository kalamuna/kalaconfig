# Kalamuna Configuration
Kalaconfig provides some configuration and module dependencies for starting new Drupal projects, including three [config split](https://www.drupal.org/project/config_split) environments - dev, test, and live.

**Note:** This module automatically uninstalls itself immediately after installation. But do not remove it! Composer dependencies are still needed.

## Usage
1. Copy the `"chosen"` package definition from the `"repositories"` section of Kalaconfig's `composer.json` into your project's root `composer.json`, as Composer does not read repositories info from dependencies.
1. Add a composer dev-dependency on `"harvesthq/chosen": "^1.0"` to your main project.
1. Run `composer install` on your main project.
1. Turn on your local development environment (e.g., `vagrant up` to use the built-in DrupalVM)
1. Install Drupal with core "standard" installation profile.
1. Remove the `$settings['install_profile'] = 'standard'` line automatically appended to `settings.php` by the Drupal install process.
1. Enable this module; it will set up a sane [Config Split](https://www.drupal.org/project/config_split) scheme, automatically export your initial configs, and then uninstall itself.
1. Commit the newly-exported configuration files to your repository, and push them to origin.
1. Use the `config_installer` installation profile to create the sites in all other environments (`drush site-install config_installer`).

## Notes
You may choose not to install dev dependencies in your production/testing/staging environments (recommended) by using `composer install --no-dev`. However, that means the dependencies brought in by Kalaconfig will be missing in those environments. Therefore, you must remember to copy any dependencies provided by Kalaconfig that are needed in those environments into your main project's composer.json "require" section.
