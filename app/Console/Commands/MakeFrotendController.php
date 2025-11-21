<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFrotendController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:FrontendController {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller inside Frontend folder extending FrontendController';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $name = $this->argument('name');
        $namespace = 'App\\Http\\Controllers\\Frontend';

        $directory = app_path('Http/Controllers/Frontend');
        $filePath = $directory . '/' . $className . '.php';

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
        }

        $stubPath = base_path('stubs/frontend.controller.stub');
        $stub = File::get($stubPath);

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $className],
            $stub
        );

        File::put($filePath, $content);

        $this->components->info("Frontend Controller created successfully.");
        $this->components->bulletList([
            "Path: " . str_replace(base_path() . '/', '', $filePath)
        ]);

        return Command::SUCCESS;
    }
}
