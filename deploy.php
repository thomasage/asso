<?php
declare(strict_types=1);

namespace Deployer;

require 'recipe/symfony.php';

// Project repository
set('repository', 'git@github.com:thomasage/asso.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', ['document']);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

inventory(__DIR__.'/hosts.yaml');

// Tasks

task(
    'build',
    function () {
        run('cd {{release_path}} && build');
    }
);

set('bin_dir', 'bin');

// [Optional] Specific to OVH

set('bin/php', '/usr/local/php7.3/bin/php');

// If deploy fails automatically unlock.

after('deploy:failed', 'deploy:unlock');
