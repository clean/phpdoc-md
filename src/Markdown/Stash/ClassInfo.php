<?php namespace Clean\PhpDocMd\Markdown\Stash;

use Clean\View\Phtml;
use Clean\PhpDocMd\ClassParser;
use ReflectionClass;

class ClassInfo extends Phtml
{
    private $reflectionClass;

    public function __construct(ReflectionClass $class)
    {
        $this->setTemplate(__DIR__.'/../../../tpl/stash.class.phtml');
        $this->reflectionClass = $class;
    }

    public function render()
    {
        $parser = new ClassParser($this->reflectionClass);
        $this->setData(
            [
                'className' => $this->reflectionClass->getName(),
                'classShortName' => $this->reflectionClass->getShortName(),
                'methods' => $parser->getMethodsDetails(),
                'classDescription' => $parser->getClassDescription(),
            ]
        );
        return parent::render();
    }
}
