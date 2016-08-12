<?php namespace Clean\PhpDocMd\Markdown;

use Clean\View\Phtml;
use Clean\PhpDocMd\ClassParser;
use ReflectionClass;

abstract class ClassInfo extends Phtml
{
    protected $reflectionClass;
    protected $config;

    /**
     * __construct 
     * 
     * @param ReflectionClass $class class 
     */
    public function __construct(ReflectionClass $class, $config = [])
    {
        $this->setTemplate(__DIR__.'/../../tpl/' . $this->getFormatName() . '.class.phtml');
        $this->reflectionClass = $class;
        $this->config = $config;
    }

    abstract public function getFormatName();

    /**
     * Renders markdown for class
     * 
     * @return string
     */
    public function render()
    {
        $parser = new ClassParser($this->reflectionClass, $this->config);
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
            ]
        );
        return parent::render();
    }
}
