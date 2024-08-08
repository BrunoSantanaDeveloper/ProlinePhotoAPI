<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteServiceAndRepository extends Command
{
    protected $signature = 'delete:sar {name}';
    protected $description = 'Delete service, repository, interface, and their tests';

    public function handle()
    {
        $name = $this->argument('name');

        $servicePath = app_path("Services/{$name}Service.php");
        $repositoryPath = app_path("Repositories/{$name}Repository.php");
        $interfacePath = app_path("Repositories/Contracts/{$name}RepositoryInterface.php");
        $serviceTestPath = base_path("tests/Unit/{$name}ServiceTest.php");
        $repositoryTestPath = base_path("tests/Unit/{$name}RepositoryTest.php");

        // Delete files if they exist
        $this->deleteFile($servicePath);
        $this->deleteFile($repositoryPath);
        $this->deleteFile($interfacePath);
        $this->deleteFile($serviceTestPath);
        $this->deleteFile($repositoryTestPath);

        $this->info("Service, Repository, Interface, and tests for {$name} have been deleted.");
    }

    private function deleteFile($path)
    {
        if (File::exists($path)) {
            File::delete($path);
            $this->info("Deleted: {$path}");
        } else {
            $this->warn("File not found: {$path}");
        }
    }
}
