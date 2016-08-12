<?php namespace Clean\PhpDocMd\Markdown;

use Clean\View\Phtml;
use Clean\PhpDocMd\ClassParser;
use ReflectionClass;

abstract class ClassInfo extends Phtml
{
    protected $reflectionClass;

    /**
     * __construct 
     * 
     * @param ReflectionClass $class class 
     */
    public function __construct(ReflectionClass $class)
    {
        $this->setTemplate(__DIR__.'/../../tpl/' . $this->getFormatName() . '.class.phtml');
        $this->reflectionClass = $class;
    }

    abstract public function getFormatName();

    /**
     * Renders markdown for class
     * 
     * @return string
     */
    public function render()
    {
        $parser = new ClassParser($this->reflectionClass);
        $methods = $parser->getMethodsDetails();
        ksort($methods);

        $this->setData(
            [
                'className' => $this->reflectionClass->getName(),
                'classShortName' => $this->reflectionClass->getShortName(),
                'methods' => $methods,
                'classDescription' => $parser->getClassDescription(),
                'interfaces' => $parser->getInterfaces(),
                'parentClass' => $parser->getParentClassName(),
                'inheritedMethods' => $parser->getInheritedMethods(),
            ]
        );
        return parent::render();
    }
}
