# Documentation

## Creating project

Before creating a project, we need to create an empty directory.

```bash
mkdir typo3-from-scratch
cd typo3-from-scratch
```

After that get the composer packages we want:

```bash
composer require --no-update "typo3/minimal:^9" helhum/typo3-secure-web typo3-console/composer-auto-commands
```

The package [helhum/typo3-secure-web](https://github.com/helhum/typo3-secure-web) divides the installation to two folders.

The folder holding the TYPO3 installation you know is in the `root-dir`.

The folder `web-dir` and therefore the document root consists only of the needed files for running TYPO3.

To separate it, we need to add some extra configuration to the `composer.json` file:

```bash
composer config extra.typo3/cms.root-dir private
composer config extra.typo3/cms.web-dir public
```

To get access to TER only extensions and the later created project extension(s) we have to add two repositories to the
`composer.json` file:

```bash
composer config repositories.0 '{"type": "path", "url": "packages/*"}'
composer config repositories.1 '{"type": "composer", "url": "https://composer.typo3.org/"}'
```

After that, run composer install:

```bash
composer install
```

## Local environment (ddev)

Using ddev, you need to have [Docker](https://ddev.readthedocs.io/en/stable/users/docker_installation/) and [ddev](https://ddev.readthedocs.io/en/stable/#installation) installed before.

To configure ddev, just initialize it by:

```bash
ddev config
```

You can just push enter when asked for three questions.
Only thing to do is to move the generated file `AdditionalConfiguration.php` afterwards due to ddev is not compatible to typo3-secure-web.
ddev generates the file in docroot where it makes no sense, since TYPO3 looks for it in `root-dir`.

```bash
mv public/typo3conf/AdditionalConfiguration.php private/typo3conf/
```

After that you can setup TYPO3:

```bash
touch private/FIRST_INSTALL
ddev start
```

Visit the given URL http://typo3-from-scratch.ddev.site to go through the setup wizard and create your admin account.

## Project extensions

To setup project extensions in a separate folder, use e.g. `packages` and add your project extensions in there.

The `packages` folder we already added to the `composer.json` before. You can change it by running:

```bash
composer config repositories.0 '{"type": "path", "url": "otherfolder/*"}'
```

Then you can add your project extension.

You only need minimum two files in your extension folder:

* ext_emconf.php
* composer.json

The composer.json needs minimum title (e.g. `vendor/sitepackage`), type (`typo3-cms-extension`) and the requirement of `typo3/cms-core`.

Then you can add it by:

```bash
composer require vendor/sitepackage:"@dev"
```

The extension will now be symlinked into typo3conf/ext/sitepackage.

## GitLab

To activate GitLab CI/CD you only need to add a `.gitlab-ci.yml` file into the root of your repository.

The YAML file defines the complete pipeline in form of stages and the correspoding jobs to do in these stages.
In this repository you can see an example of a short build.

More information you can get in [the official documentation](https://docs.gitlab.com/ce/ci/yaml/)

Examples:

* [typo3.org pipeline](https://git-t3o.typo3.org/t3o/t3olayout/blob/master/Configuration/GitLab/t3o-builds.yml)
