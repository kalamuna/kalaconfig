# Kalamuna Configuration
Provides a base set of configuration and module dependencies for starting new Drupal projects. After installing this module once, you may uninstall it but do not remove it, as composer dependencies are still needed.

Right now, it just turns on a few modules and creates two [config split](https://www.drupal.org/project/config_split) environments - dev and test. It does not actually provide any of the configuration for those environments; this is up to you. For an example, see the [dev](https://github.com/kalamuna/drupal-project/tree/8.x-kala/config/dev) and [test](https://github.com/kalamuna/drupal-project/tree/8.x-kala/config/test]) config folders in Kalamuna's [fork](https://github.com/kalamuna/drupal-project) of the [drupal-project](https://github.com/drupal-composer/drupal-project/) repository.

## Usage
1. Install Drupal with Drupal core's "standard" installation profile.
1. Enable this module; it will set up a sane [Config Split](https://www.drupal.org/project/config_split) scheme, automatically export your initial configs, and then uninstall itself.
1. Commit the newly-exported configuration files to your repository.
