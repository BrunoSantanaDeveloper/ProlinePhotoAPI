<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceAndRepository extends Command
{
    protected $signature = 'make:sar {name}';
    protected $description = 'Create service, repository, interface, and their tests';

    public function handle()
    {
        $name = $this->argument('name');

        $serviceNamespace = "App\\Services";
        $repositoryNamespace = "App\\Repositories";
        $interfaceNamespace = "App\\Repositories\\Contracts";
        $serviceClass = "{$name}Service";
        $repositoryClass = "{$name}Repository";
        $repositoryInterface = "{$repositoryClass}Interface";

        // Define paths
        $servicePath = app_path("Services/{$serviceClass}.php");
        $repositoryPath = app_path("Repositories/{$repositoryClass}.php");
        $interfacePath = app_path("Repositories/Contracts/{$repositoryInterface}.php");
        $serviceTestPath = base_path("tests/Unit/{$serviceClass}Test.php");
        $repositoryTestPath = base_path("tests/Unit/{$repositoryClass}Test.php");

        // Ensure directories exist
        $this->ensureDirectoryExists(dirname($servicePath));
        $this->ensureDirectoryExists(dirname($repositoryPath));
        $this->ensureDirectoryExists(dirname($interfacePath));
        $this->ensureDirectoryExists(dirname($serviceTestPath));
        $this->ensureDirectoryExists(dirname($repositoryTestPath));

        // Generate files
        $this->generateFile('service.stub', $servicePath, [
            '{{ namespace }}' => $serviceNamespace,
            '{{ class }}' => $serviceClass,
            '{{ repositoryInterface }}' => "{$interfaceNamespace}\\{$repositoryInterface}",
        ]);

        $this->generateFile('repository.stub', $repositoryPath, [
            '{{ namespace }}' => $repositoryNamespace,
            '{{ class }}' => $repositoryClass,
            '{{ interface }}' => "{$interfaceNamespace}\\{$repositoryInterface}",
        ]);

        $this->generateFile('repository-interface.stub', $interfacePath, [
            '{{ namespace }}' => $interfaceNamespace,
            '{{ class }}' => $repositoryInterface,
        ]);

        $this->generateFile('service-test.stub', $serviceTestPath, [
            '{{ serviceNamespace }}' => "{$serviceNamespace}\\{$serviceClass}",
            '{{ repositoryNamespace }}' => "{$repositoryNamespace}\\{$repositoryClass}",
            '{{ serviceClass }}' => $serviceClass,
            '{{ repositoryClass }}' => $repositoryClass,
            '{{ class }}' => "{$serviceClass}Test",
        ]);

        $this->generateFile('repository-test.stub', $repositoryTestPath, [
            '{{ repositoryNamespace }}' => "{$repositoryNamespace}\\{$repositoryClass}",
            '{{ repositoryClass }}' => $repositoryClass,
            '{{ class }}' => "{$repositoryClass}Test",
        ]);

        $this->info("Service, Repository, Interface, and tests for {$name} created successfully.");
    }

    private function generateFile($stub, $path, $replacements)
    {
        $stubContent = File::get(base_path("stubs/{$stub}"));

        foreach ($replacements as $key => $value) {
            $stubContent = str_replace($key, $value, $stubContent);
        }

        File::put($path, $stubContent);
    }

    private function ensureDirectoryExists($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
}
