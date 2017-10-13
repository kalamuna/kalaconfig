# Kalamuna Configuration
Provides a base set of configuration and module dependencies for starting new Drupal projects. After installing this module once, you may uninstall it but do not remove it, as composer dependencies are still needed.

Right now, it just turns on a few modules and creates two [config split](https://www.drupal.org/project/config_split) environments - dev and test. It does not actually provide any of the configuration for those environments; this is up to you. For an example, see the [dev](https://github.com/kalamuna/drupal-project/tree/8.x-kala/config/dev) and [test](https://github.com/kalamuna/drupal-project/tree/8.x-kala/config/test]) config folders in Kalamuna's [fork](https://github.com/kalamuna/drupal-project) of the [drupal-project](https://github.com/drupal-composer/drupal-project/) repository.

## Usage
1. Copy the "chosen" package definition from the "repositories" section of Kalaconfig's composer.json into your project's main composer.json, as Composer does not read repositories from project dependencies.
1. Add a composer dev-dependency on `"harvesthq/chosen": "~1.0"` to your main project.
1. Run `composer install` on your main project.
1. Turn on your local development environment (e.g., `vagrant up` to use the built-in DrupalVM)
1. Install Drupal with core "standard" installation profile.
1. Enable this module; it will set up a sane [Config Split](https://www.drupal.org/project/config_split) scheme, automatically export your initial configs, and then uninstall itself.
1. Commit the newly-exported configuration files to your repository.

## Notes
You may choose not to install dev dependencies in your production/testing/staging environments by using `composer install --no-dev` (recommended). However, that means the dependencies brought in by Kalaconfig will be missing in those environments. Therefore, you must remember to copy any dependencies provided by Kalaconfig that are needed in those environments into your main project's composer.json "require" section.
