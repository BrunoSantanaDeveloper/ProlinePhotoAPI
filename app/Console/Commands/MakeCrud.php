<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name}';
    protected $description = 'Create service, repository, interface, controller, and tests with CRUD';

    public function handle()
    {
        $name = $this->argument('name');
        $pluralName = Str::plural(strtolower($name)); // Exemplo: Categoria -> categorias

        $serviceNamespace = "App\\Services";
        $repositoryNamespace = "App\\Repositories";
        $interfaceNamespace = "App\\Repositories\\Contracts";
        $controllerNamespace = "App\\Http\\Controllers";
        $modelNamespace = "App\\Models";
        $serviceClass = "{$name}Service";
        $repositoryClass = "{$name}Repository";
        $repositoryInterface = "{$repositoryClass}Interface";
        $controllerClass = "{$name}Controller";
        $modelClass = $name;

        // Define paths
        $servicePath = app_path("Services/{$serviceClass}.php");
        $repositoryPath = app_path("Repositories/{$repositoryClass}.php");
        $interfacePath = app_path("Repositories/Contracts/{$repositoryInterface}.php");
        $controllerPath = app_path("Http/Controllers/{$controllerClass}.php");
        $routesPath = base_path("routes/api.php");

        // Ensure directories exist
        $this->ensureDirectoryExists(dirname($servicePath));
        $this->ensureDirectoryExists(dirname($repositoryPath));
        $this->ensureDirectoryExists(dirname($interfacePath));
        $this->ensureDirectoryExists(dirname($controllerPath));

        // Generate files
        $this->generateFile('service.stub', $servicePath, [
            '{{ namespace }}' => $serviceNamespace,
            '{{ class }}' => $serviceClass,
            '{{ repositoryInterface }}' => "{$interfaceNamespace}\\{$repositoryInterface}",
            '{{ repository }}' => "{$repositoryInterface}",
        ]);

        $this->generateFile('repository.stub', $repositoryPath, [
            '{{ namespace }}' => $repositoryNamespace,
            '{{ class }}' => $repositoryClass,
            '{{ interface }}' => "{$interfaceNamespace}\\{$repositoryInterface}",
            '{{ interfaceClass }}' => "{$repositoryInterface}",
            '{{ modelNameSpace }}' => "{$modelNamespace}\\{$modelClass}",
            '{{ model }}' => "{$modelClass}",
        ]);

        $this->generateFile('repository-interface.stub', $interfacePath, [
            '{{ namespace }}' => $interfaceNamespace,
            '{{ class }}' => $repositoryInterface,
        ]);

        $this->generateFile('controller.stub', $controllerPath, [
            '{{ namespace }}' => $controllerNamespace,
            '{{ class }}' => $controllerClass,
            '{{ service }}' => "{$serviceNamespace}\\{$serviceClass}",
            '{{ serviceClass }}' => "{$serviceClass}",
            '{{ request }}' => "Illuminate\Http\Request",
        ]);

        // Add routes to api.php
        $this->appendRoutes($routesPath, $pluralName, $controllerClass);
        $this->addBindingToServiceProvider($repositoryInterface, $repositoryClass);

        $this->info("CRUD files and routes for {$name} created successfully.");
    }


    private function addBindingToServiceProvider($interface, $implementation)
    {
        $providerPath = app_path('Providers/AppServiceProvider.php');

        if (File::exists($providerPath)) {
            $providerContent = File::get($providerPath);

            // Crie a linha de código para vincular a interface ao repositório
            $binding = "\$this->app->bind(\\App\\Repositories\\Contracts\\{$interface}::class, \\App\\Repositories\\{$implementation}::class);";

            // Verifique se a linha já existe
            if (Str::contains($providerContent, $binding)) {
                $this->info("Binding already exists in AppServiceProvider.");
                return;
            }

            // Localize o método register() e insira a linha de código
            $search = 'public function register()';
            $replace = "public function register()\n    {\n        {$binding}\n";

            // Substitua a primeira ocorrência do método register()
            $providerContent = Str::replaceFirst($search, $replace, $providerContent);

            // Salve o arquivo atualizado
            File::put($providerPath, $providerContent);

            $this->info("Binding added to AppServiceProvider.");
        } else {
            $this->error("AppServiceProvider.php not found!");
        }
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

    private function appendRoutes($pluralName, $controllerClass)
    {
        $routesPath = base_path('routes/api.php');

        $controllerName = ucfirst(Str::singular($controllerClass));
        if (File::exists($routesPath)) {
            $routeDefinition = "Route::apiResource('{$controllerClass}', App\\Http\\Controllers\\{$controllerName}Controller::class);";

            // Verifique se a rota já existe
            $routesContent = File::get($routesPath);
            if (Str::contains($routesContent, $routeDefinition)) {
                $this->info("Route already exists in api.php.");
                return;
            }

            // Adicionar a rota ao final do arquivo api.php
            File::append($routesPath, "\n" . $routeDefinition);

            $this->info("Route added to api.php for {$pluralName}.");
        } else {
            $this->error("api.php not found!");
        }
    }
}
