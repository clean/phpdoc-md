<?php namespace Clean\PhpDocMd;

use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;

class ClassParser
{
    /**
     * @var ReflectionClass
     */
    private $reflection;
    private $docBlockFactory;

    public function __construct(ReflectionClass $class)
    {
        $this->reflection = $class;
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function getClassDescription()
    {
        $docblock = $this->docBlockFactory->create($this->reflection->getDocComment() ?: '/** */');
        return (object)[
            'short' => (string)$docblock->getSummary(),
            'long' => (string)$docblock->getDescription(),
        ];
    }

    public function getParentClassName()
    {
        return ($p = $this->reflection->getParentClass()) ? $p->getName() : null;
    }

    public function getInterfaces()
    {
        return $this->reflection->getInterfaceNames();
    }

    public function getMethodsDetails()
    {
        $methods = [];
        $parentClassMethods = $this->getInheritedMethods();

        foreach ($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (isset($parentClassMethods[$method->getName()])) {
                continue;
            }
            $methods[$method->getName()] = $this->getMethodDetails($method);
        }

        return $methods;
    }

    private function getMethodDetails($method)
    {
        $docblock = $this->docBlockFactory->create($method->getDocComment() ?: '/** */');

        if ($docblock->getSummary()) {
            $data = [
                'shortDescription' => $docblock->getSummary(),
                'longDescription' => $docblock->getDescription(),
                'argumentsList' => $this->retrieveParams($docblock->getTagsByName('param')),
                'argumentsDescription' => $this->retrieveParamsDescription($docblock->getTagsByName('param')),
                'returnValue' => $this->retrieveTagData($docblock->getTagsByName('return')),
                'throwsExceptions' => $this->retrieveTagData($docblock->getTagsByName('throws')),
                'visibility' =>  join(
                    '',
                    [
                        $method->isFinal() ? 'final ' : '',
                        'public',
                        $method->isStatic() ? ' static' : '',
                    ]
                ),
            ];
        } else {
            $data = [];
            $className = sprintf("%s::%s", $method->class, $method->name);
            $atlasdoc = new \Clean\PhpAtlas\ClassMethod($className);
            $data['shortDescription'] = $atlasdoc->getMethodShortDescription();
            $data['doclink'] = $atlasdoc->getMethodPHPDocLink();
        }
        return (object)$data;
    }

    public function getInheritedMethods()
    {
        $methods = [];
        $parentClass = $this->reflection->getParentClass();
        if ($parentClass) {
            foreach ($parentClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methods[$method->getName()] = $this->getMethodDetails($method);
            }
        }
        ksort($methods);
        return $methods;
    }

    private function retrieveTagData(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = (object)[
                'desc' => $param->getDescription(),
                'type' => $param->getType(),
            ];
        }
        return $data;
    }

    private function retrieveParams(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = sprintf("%s $%s", $param->getType(), $param->getVariableName());
        }
        return $data;
    }

    private function retrieveParamsDescription(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = (object)[
                'name' => '$' . $param->getVariableName(),
                'desc' => $param->getDescription(),
                'type' => $param->getType(),
            ];
        }
        return $data;
    }

    private function getPHPDocLink($method) {
        return strtolower(sprintf('https://secure.php.net/manual/en/%s.%s.php', $method->class, $method->name));
    }
}
