<?php

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\DatabaseBackup;
use Illuminate\Support\Facades\Schedule;

// this does the database backups weekly
Schedule::call(function () {
    $backup = new Controller();
    $database_backup_configuration = \collect(optional(collect(Cache::get('settings'))
        ->where('key', 'database_backup_configuration')->first())->getSettingValue());
    $database_backup = Setting::where('key', 'database_backup')->first()
        ->getSettingValue('last') ?? 0;
    if ($database_backup == 1) {
        \collect(
            optional(collect(Cache::get('settings'))
                ->where('key', 'database_configuration')->first())
                ->getSettingValue('last')
        )
            ->filter(fn($value, $key) => in_array($key, $database_backup_configuration->toArray()))
            ->each(function ($item, $key) use ($backup) {
                $backup->backupDatabase(
                    $item->get('DB_DATABASE'),
                    $item->get('DB_USERNAME'),
                    $item->get('DB_PASSWORD'),
                    $item->get('DB_HOST'),
                    $item->get('DB_PORT')
                );
            });
    }
})->weekly();

