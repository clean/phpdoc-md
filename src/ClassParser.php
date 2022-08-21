<?php namespace Clean\PhpDocMd;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
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

        $data = [
            'shortDescription' => null,
            'longDescription' => null,
            'argumentsList' => [],
            'argumentsDescription' => null,
            'returnValue' => null,
            'throwsExceptions' => null,
            'visibility' => null,
        ];

        if ($docblock->getSummary()) {
            $data['shortDescription'] = $docblock->getSummary();
            $data['longDescription'] = $docblock->getDescription();
            $data['argumentsList'] = $this->retrieveParams($docblock->getTagsByName('param'));
            $data['argumentsDescription'] = $this->retrieveParamsDescription($docblock->getTagsByName('param'));
            $data['returnValue'] = $this->retrieveTagData($docblock->getTagsByName('return'));
            $data['throwsExceptions'] = $this->retrieveTagData($docblock->getTagsByName('throws'));
            $data['visibility'] =  join(
                    '',
                    [
                        $method->isFinal() ? 'final ' : '',
                        'public',
                        $method->isStatic() ? ' static' : '',
                    ]
                );
        } else {
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
            if ($param instanceof InvalidTag) {
                continue;
            }
            $data[] = sprintf("%s $%s", $param->getType(), $param->getVariableName());
        }
        return $data;
    }

    private function retrieveParamsDescription(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            if ($param instanceof InvalidTag) {
                continue;
            }
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
