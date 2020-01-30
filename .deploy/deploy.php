<?php

namespace Deployer;

require_once '/tmp/vendor/deployer/deployer/recipe/common.php';
require_once '/tmp/vendor/deployer/recipes/recipe/rsync.php';

$sharedDirectories = [
    'private/fileadmin',
    'private/uploads',
    'var/labels'
];
set('shared_dirs', $sharedDirectories);

$sharedFiles = [
    'private/typo3conf/AdditionalConfiguration.php'
];
set('shared_files', $sharedFiles);

$writeableDirectories = [
    'public/typo3temp',
];
set('writable_dirs', $writeableDirectories);

$exclude = [
    '.gitignore',
    '.git',
    'Readme.rst',
    'Readme.txt',
    'Upgrading.rst',
    'Upgrading.txt',
    'README',
    '*.example',
    'AdditionalConfiguration.ddev.php'
];
set('rsync', [
    'exclude' => array_merge($sharedDirectories, $sharedFiles, $exclude),
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'avz',
    'options' => ['delete'],
    'timeout' => 300
]);
set('rsync_src', './');
set('keep_releases', 5);
set('typo3_console', './vendor/bin/typo3cms');
set('cache_file', 'cache.php');
set('cache_file_content', '<?php opcache_reset();');

inventory('.deploy/hosts.yml');

task('typo3', function () {
    run('cd {{release_path}} && {{typo3_console}} install:generatepackagestates');
    run('cd {{release_path}} && {{typo3_console}} install:extensionsetupifpossible');
});

task('opcache', function () {
    run('cd {{release_path}} && echo "{{cache_file_content}}" > public/{{cache_file}}');
    run('curl -s https://{{hostname}}/{{cache_file}} > /dev/null');
    run('cd {{release_path}} && rm -f public/{{cache_file}}');
});

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'rsync:warmup',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'typo3',
    'deploy:symlink',
    'opcache',
    'cleanup',
]);
