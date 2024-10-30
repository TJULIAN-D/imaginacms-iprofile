<?php

namespace Modules\Iprofile\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Iprofile\Events\Handlers\RegisterIprofileSidebar;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel as SentinelCartalyst;
use Modules\Iprofile\Http\Middleware\AuthCan;
use Modules\Iprofile\Http\Middleware\SettingMiddleware;
use Modules\Iprofile\Http\Middleware\OptionalAuth;
use Socialite;
use Modules\Iprofile\Services\SocialiteGoogleJWTProvider;

class IprofileServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    protected $middleware = [
        'setting-can' => SettingMiddleware::class,
        'auth-can' => AuthCan::class,
        'optional-auth' => OptionalAuth::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIprofileSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('fields', Arr::dot(trans('iprofile::fields')));
            $event->load('addresses', Arr::dot(trans('iprofile::addresses')));
            $event->load('departments', Arr::dot(trans('iprofile::departments')));
            $event->load('settings', Arr::dot(trans('iprofile::settings')));
            $event->load('userdepartments', Arr::dot(trans('iprofile::userdepartments')));
        });


    }

    public function boot()
    {
        $this->registerMiddleware();
        $this->publishConfig('iprofile', 'config');
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'settings'), "asgard.iprofile.settings");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'settings-fields'), "asgard.iprofile.settings-fields");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'permissions'), "asgard.iprofile.permissions");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'cmsPages'), "asgard.iprofile.cmsPages");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'cmsSidebar'), "asgard.iprofile.cmsSidebar");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'gamification'), "asgard.iprofile.gamification");
        $this->mergeConfigFrom($this->getModuleConfigFilePath('iprofile', 'blocks'), "asgard.iprofile.blocks");

        $this->publishConfig('iprofile', 'crud-fields');
        //$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerComponents();
        $this->registerComponentsLivewire();

        //Include custom provider for Socialite
        Socialite::extend('google-jwt', function ($app) {
            $config = $app['config']['services.google'];
            return new SocialiteGoogleJWTProvider($app['request'], $config['client_id'], $config['client_secret'], $config['redirect']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Iprofile\Repositories\FieldRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentFieldRepository(new \Modules\Iprofile\Entities\Field());
                if (!config('app.cache')) {
                    return $repository;
                }
                return new \Modules\Iprofile\Repositories\Cache\CacheFieldDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\AddressRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentAddressRepository(new \Modules\Iprofile\Entities\Address());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheAddressDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\DepartmentRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentDepartmentRepository(new \Modules\Iprofile\Entities\Department());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheDepartmentDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Iprofile\Repositories\SettingRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentSettingRepository(new \Modules\Iprofile\Entities\Setting());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheSettingDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\UserDepartmentRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentUserDepartmentRepository(new \Modules\Iprofile\Entities\UserDepartment());
                if (!config('app.cache')) {
                    return $repository;
                }
                return new \Modules\Iprofile\Repositories\Cache\CacheUserDepartmentDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\RoleApiRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentRoleApiRepository(new \Modules\Iprofile\Entities\Role());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheRoleApiDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\UserApiRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentUserApiRepository(new \Modules\User\Entities\Sentinel\User());
                if (!config('app.cache')) {
                    return $repository;
                }
                return new \Modules\Iprofile\Repositories\Cache\CacheUserApiDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\ProviderAccountRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentProviderAccountRepository(new \Modules\Iprofile\Entities\ProviderAccount());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheProviderAccountDecorator($repository);
            }
        );

        $this->app->bind(
            'Modules\Iprofile\Repositories\UserPasswordHistoryRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentUserPasswordHistoryRepository(new \Modules\Iprofile\Entities\UserPasswordHistory());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheUserPasswordHistoryDecorator($repository);
            }
        );
                $this->app->bind(
            'Modules\Iprofile\Repositories\InformationRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentInformationRepository(new \Modules\Iprofile\Entities\Information());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheInformationDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iprofile\Repositories\SkillRepository',
            function () {
                $repository = new \Modules\Iprofile\Repositories\Eloquent\EloquentSkillRepository(new \Modules\Iprofile\Entities\Skill());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iprofile\Repositories\Cache\CacheSkillDecorator($repository);
            }
        );
// add bindings


    }

    private function registerMiddleware()
    {
        foreach ($this->middleware as $name => $class) {
            $this->app['router']->aliasMiddleware($name, $class);
        }
    }

    /**
     * Register components Livewire
     */
    private function registerComponents()
    {
        Blade::componentNamespace("Modules\Iprofile\View\Components", 'iprofile');
    }

    /**
     * Register components Livewire
     */
    private function registerComponentsLivewire()
    {
        Livewire::component('iprofile::address-form', \Modules\Iprofile\Http\Livewire\AddressForm::class);
        Livewire::component('iprofile::address-list', \Modules\Iprofile\Http\Livewire\AddressList::class);
        Livewire::component('iprofile::user-menu', \Modules\Iprofile\Http\Livewire\UserMenu::class);
    }


}