<?php namespace Clean\PhpDocMd;

use ReflectionClass;

class PhpDocMdCommand
{

    private $configFile = '.phpdoc-md';
    private $config;


    public function __construct()
    {
        $this->config = require $this->configFile;

        if (is_array($this->config)) {
            $this->config = (object) $this->config;
        }
    }

    public function execute()
    {
        switch ($this->config->format) {
            default:
                $readme = new Markdown\GitHub\Readme($this->config->rootNamespace);
        }

        foreach ($this->config->classes as $class) {
            try {
                $reflection = new ReflectionClass($class);
            } catch (\ReflectionException $e) {
                $this->error('Config error: ' . $e->getMessage(), $e);
            }
            $destDir = $this->getDestinationDirectory(
                $reflection,
                $this->config->rootNamespace,
                $this->config->destDirectory
            );
            $destFile = sprintf('%s.md', $reflection->getShortName());
            switch ($this->config->format) {
                case 'stash':
                    $markDown = new Markdown\Stash\ClassInfo($reflection);
                    break;
                case 'github':
                    $markDown = new Markdown\GitHub\ClassInfo($reflection);
                    break;
                default:
                    throw new \RuntimeException('Not supported markdown format given: ' . $this->config->format);
            }

            file_exists($destDir) ?: mkdir($destDir, 0777, true);
            file_put_contents($destDir . DIRECTORY_SEPARATOR . $destFile, $markDown);
            $readme->addLink(
                $this->removeRootNamespace($reflection->getName(), $this->config->rootNamespace),
                $this->namespaceToPath(
                    $this->removeRootNamespace(
                        $reflection->getName(),
                        $this->config->rootNamespace
                    )
                ) . '.md'
            );
        }

        file_put_contents($this->config->destDirectory . '/README.md', $readme);
    }

    protected function getDestinationDirectory(ReflectionClass $reflection, $rootNamespace, $rootDir)
    {
        return $this->namespaceToPath(
            rtrim(
                sprintf(
                    '%s/%s',
                    $rootDir,
                    $this->removeRootNamespace($reflection->getNamespaceName(), $rootNamespace)
                ),
                '/'
            )
        );
    }

    protected function namespaceToPath($namespace)
    {
        return str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            $namespace
        );
    }

    protected function removeRootNamespace($namespace, $root)
    {
        $re = preg_replace(
            sprintf("/^%s/", addslashes($root)),
            '',
            $namespace
        );
        return ltrim($re, '\\');
    }

    protected function error($message, $exception = null)
    {
        echo sprintf("ERROR: '%s'", trim($message));
        exit(1);
    }
}
