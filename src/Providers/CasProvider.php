<?php

namespace Buqiu\Providers\Cas;

use Illuminate\Support\ServiceProvider;

class CasProvider extends ServiceProvider
{
    /**
     * NotesCn: 注册应用程序服务。
     * NotesEn: Register the application services.
     * User : smallK
     * Date : 2022/1/6
     * Time : 3:25 下午
     */
    public function register()
    {

    }

    /**
     * NotesCn: 引导应用程序服务
     * NotesEn: Bootstrap the application services.
     * User : smallK
     * Date : 2022/1/6
     * Time : 3:26 下午
     */
    public function boot()
    {
        // Config path.
        $configPath = __DIR__.'/../../config/cas.php';
        // Migrations path.
        $migrationsPath = __DIR__.'/../../database/migrations/';

        // Publish config.
        $this->publishes(
            [
                $configPath => config_path('cas.php'),
            ],
            'cas');

        $this->publishes(
            [
                $migrationsPath => database_path('migrations'),
            ],
            'migrations'
        );
    }
}