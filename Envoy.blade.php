
@servers(['web1' => ['lojarose@191.252.194.142']])

@setup
$path = '/var/www/html/loja_rose'

@endsetup

{{-- @story('deploy')
    git
    composer
    configs
    restart
@endstory --}}

@task('deploy')
cd {{ $path }}
git reset --hard origin/main
git pull
php artisan migrate --force
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
@endtask
{{--
@task('composer')
    cd {{ $path }}

@endtask

@task('configs')
    cd {{ $path }}

@endtask

@task('restart')

@endtask --}}
