<?php

namespace Deployer;

require_once __DIR__ . '/common.php';

add('recipes', ['silverstripe']);

/**
 * Silverstripe configuration
 */

set('shared_assets', function () {
    if (test('[ -d {{release_or_current_path}}/public ]') || test('[ -d {{deploy_path}}/shared/public ]')) {
        return 'public/assets';
    }
    return 'assets';
});


// Silverstripe shared dirs
set('shared_dirs', [
    '{{shared_assets}}',
]);

// Silverstripe writable dirs
set('writable_dirs', [
    '{{shared_assets}}',
]);

// Retain silverstripe_cli_script for compatibility with overrides
set('silverstripe_cli_script', 'vendor/bin/sake');

// Silverstripe version detection 
set('silverstripe6', test('[ -f {{release_or_current_path}}/vendor/silverstripe/framework/bin/sake ]'));

// dev/build or db:build
set('silverstripe_build_command', function () {
    if (get('silverstripe6')) {
        return 'db:build';
    }
    return 'dev/build';
});

// flush=1 or --flush
set('silverstripe_flush_option', function () {
    if (get('silverstripe6')) {
        return '--flush';
    }
    return 'flush=1';
});

/**
 * Helper tasks
 */
desc('Runs dev/build or db:build as appropriate');
task('silverstripe:build', function () {
    run('{{release_or_current_path}}/{{silverstripe_cli_script}} {{silverstripe_build_command}}');
});

desc('Runs dev/build?flush=all or db:build --flush as appropriate');
task('silverstripe:buildflush', function () {
    run('{{release_or_current_path}}/{{silverstripe_cli_script}} {{silverstripe_build_command}} {{silverstripe_flush_option}}');
});

/**
 * Main task
 */
desc('Deploys your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'silverstripe:buildflush',
    'deploy:publish',
]);
