<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';

/*
|--------------------------------------------------------------------------
| Deployment (Deployer 8)
|--------------------------------------------------------------------------
|
| Zero-downtime deploys: every deploy goes into a new folder under
| {deploy_path}/releases, and {deploy_path}/current is switched over once
| everything succeeded. storage/ and .env live in {deploy_path}/shared.
|
| The code (including the production vendor/ folder) is built in GitHub
| Actions and uploaded with rsync, so the server needs neither git access
| to the repository nor composer.
|
| Connection settings come from environment variables (GitHub secrets).
|
*/

set('application', 'gouwwaas.be');
set('keep_releases', 5);

// Shared hosting: PHP runs as the SSH user, so no ACL/chown juggling needed.
set('writable_mode', 'skip');

// The CI pipeline writes the production .env to this local file.
set('dotenv_source', getenv('DEPLOY_DOTENV_FILE') ?: __DIR__ . '/.env.production');

set('rsync_src', __DIR__);
set('rsync', [
    'exclude' => [
        '.git',
        '.github',
        '.idea',
        '.junie',
        '/.env',
        '/.env.production',
        '/deploy.php',
        '/node_modules',
        '/tests',
        '/phpunit.xml',
        '/storage/logs/*.log',
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'rzl',
    'options' => ['delete'],
    'timeout' => 600,
]);

host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: 'example.com')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'deploy')
    ->setPort((int) (getenv('DEPLOY_PORT') ?: 22))
    ->set('deploy_path', getenv('DEPLOY_PATH') ?: '~/gouwwaas.be');

if (getenv('DEPLOY_PHP_BIN')) {
    set('bin/php', getenv('DEPLOY_PHP_BIN'));
}

// Upload the code built in CI instead of cloning the repository on the server.
task('deploy:update_code', function () {
    invoke('rsync');
});

// vendor/ is already built (--no-dev) in CI and uploaded with the code.
task('deploy:vendors', function () {
    writeln('Skipping composer install: vendor/ was built in CI.');
});

desc('Uploads the production .env generated from GitHub secrets');
task('deploy:upload_env', function () {
    $source = get('dotenv_source');

    if (! file_exists($source)) {
        throw error("Missing $source; cannot write the production .env.");
    }

    run('mkdir -p {{deploy_path}}/shared');
    upload($source, '{{deploy_path}}/shared/.env');
    run('chmod 600 {{deploy_path}}/shared/.env');
});

before('deploy:shared', 'deploy:upload_env');

after('deploy:failed', 'deploy:unlock');
