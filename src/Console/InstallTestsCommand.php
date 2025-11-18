<?php

namespace Tapp\FilamentForum\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'filament-forum:install-tests
                            {--force : Overwrite existing test files}';

    /**
     * The console command description.
     */
    protected $description = 'Install Filament Forum test files into your application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filesystem = new Filesystem;

        // Ensure tests directory exists
        $filesystem->ensureDirectoryExists(base_path('tests/Feature'));

        // Copy test files
        $testFiles = [
            'FilamentForumTest.php',
            'FilamentForumTenancyTest.php',
        ];

        foreach ($testFiles as $testFile) {
            $source = __DIR__.'/../../stubs/tests/Feature/'.$testFile;
            $destination = base_path('tests/Feature/'.$testFile);

            if (file_exists($destination) && ! $this->option('force')) {
                if (! $this->confirm("The file {$testFile} already exists. Do you want to overwrite it?")) {
                    $this->components->info("Skipped: {$testFile}");

                    continue;
                }
            }

            $filesystem->copy($source, $destination);
            $this->components->info("Published: {$testFile}");
        }

        $this->newLine();
        $this->components->info('Filament Forum tests published successfully!');
        $this->newLine();

        // Show next steps
        $this->line('Next steps:');
        $this->line('1. Make sure you have Pest installed: composer require pestphp/pest --dev');
        $this->line('2. Run the tests: php artisan test --filter=FilamentForum');
        $this->newLine();

        // Check if tenancy is enabled and show relevant message
        if (config('filament-forum.tenancy.enabled')) {
            $tenantModel = config('filament-forum.tenancy.model');
            $this->components->info("Tenancy is enabled with model: {$tenantModel}");
            $this->line('Make sure your tenant model has a factory for the tenancy tests to work.');
        } else {
            $this->components->warn('Tenancy is not enabled. Tenancy tests will be skipped.');
            $this->line('To enable tenancy, set filament-forum.tenancy.enabled to true in your config.');
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
