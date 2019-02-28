<?php

namespace MikeZange\LaravelEntityRepositories\Commands;

use Illuminate\Console\Command;

class GenerateRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:repository
                            {entity : Entity / Model Name}
                            {--s|namespace= : Namespace to create the repository in}
                            {--r|repository= : Customise the repository name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto generate a repository';

    protected $options;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->parseOptions();

        $this->createFiles();

    }

    /**
     *  Get the options from the command for later
     */
    private function parseOptions()
    {
        $entity = strtolower($this->argument('entity'));
        $repositoryDefault = ucfirst($entity);

        $this->options = [
            'entity' => $entity,
            'namespace' => ($this->option('namespace') ?? "App\Repositories"),
            'repository' => $this->option('repository') ?? "{$repositoryDefault}Repository",
            'decorator' => $this->option('repository') ?? "{$repositoryDefault}Decorator",
        ];
    }

    /**
     * Create the files!
     */
    private function createContentFromStub($stub)
    {
        $fileTemplate = file_get_contents(__DIR__."/../../stubs/{$stub}.stub");

        $replacements = [
            "{{ namespace }}" => $this->options['namespace'],
            "{{ entity }}" => $this->options['entity'],
            "{{ repository }}" => $this->options['repository'],
            "{{ decorator }}" => $this->options['decorator'],
        ];

        $fileContents = strtr($fileTemplate, $replacements);

        return $fileContents;
    }

    private function createFiles()
    {
        $root = $this->parseNamespace();

        $structure = [
            "/" => ["contract"],
            'Eloquent' => ["eloquent"],
            'Cache' => ["cache"]
        ];

        foreach ($structure as $folder => $files){
            foreach ($files as $file){

                $fileContents = $this->createContentFromStub($file);

                $folder = ($folder !== "/" ? "/{$folder}" : "");

                $fileName = ($file !== "contract" ? ucwords($file) : "") . ($file === "cache" ? $this->options['decorator'] : $this->options['repository']) . ".php";

                $path = "{$root}{$folder}";

                if(!is_dir($path)){
                   $created = mkdir($path, 0755, true);
                }

                file_put_contents("{$path}/{$fileName}" , $fileContents);
            }
        }
    }

    private function parseNamespace()
    {
        $parts = explode("\\", $this->options['namespace']);

        $base = strtolower(array_shift($parts));
        $path = implode("/", $parts);

        return base_path("{$base}/{$path}");
    }
}
