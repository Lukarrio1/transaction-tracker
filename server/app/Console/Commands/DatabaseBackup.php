<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseBackup extends Command {
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'app:database-backup';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Backs up a selected database';

    /**
    * Execute the console command.
    */

    public function handle() {
        // Get the database connection settings
        $databaseName = env( 'DB_DATABASE' );
        $user = env( 'DB_USERNAME' );
        $password = env( 'DB_PASSWORD' );
        $host = env( 'DB_HOST' );
        $port = env( 'DB_PORT', 3306 );

        // Define the backup file path
        $backupPath = $this->option( 'path' ) ?: storage_path( 'app/backups' );
        if ( !File::isDirectory( $backupPath ) ) {
            File::makeDirectory( $backupPath, 0755, true );
        }

        // Define the backup file name
        $fileName = $databaseName . '_' . date( 'Y-m-d_H-i-s' ) . '.sql';

        // Create the command to dump the database
        $command = sprintf(
            'mysqldump --host=%s --port=%d --user=%s --password=%s %s > %s',
            $host,
            $port,
            $user,
            $password,
            $databaseName,
            $backupPath . '/' . $fileName
        );

        // Execute the command
        try {
            exec( $command );
            $this->info( 'Database backup created successfully.' );
        } catch ( \Exception $e ) {
            $this->error( 'Failed to create database backup: ' . $e->getMessage() );
        }
    }
}
