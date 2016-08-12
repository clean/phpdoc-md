<?php namespace Clean\PhpDocMd;

use phpDocumentor\Reflection\DocBlock;
use ReflectionClass;
use ReflectionMethod;

class ClassParser
{
    /**
     * @var ReflectionClass
     */
    private $reflection;

    private $config;

    public function __construct(ReflectionClass $class, $config = [])
    {
        $this->reflection = $class;
        $this->config = $config;
    }

    public function getClassDescription()
    {
        $docblock = new DocBlock($this->reflection);
        $parentClassName = ($p = $this->reflection->getParentClass()) ? $p->getName() : null;
        return (object)[
            'short' => (string)$docblock->getShortDescription(),
            'long' => (string)$docblock->getLongDescription(),
            'parentClassName' => $parentClassName,
            'interfaces' => $this->getInterfaces(),
        ];
    }

    private function getInterfaces()
    {
        $interfaces = [];
        foreach ($this->reflection->getInterfaceNames() as $interface) {
            $interfaces[$interface] = $this->getClassDocHref($interface);
        }
        return $interfaces;
    }

    private function getClassDocHref($className)
    {
        if (false === strpos($className, '\\')) {
            return sprintf("http://php.net/manual/en/class.%s.php)", strtolower($className));
        }
    }

    public function getMethodsDetails()
    {
        $methods = [];
        $parentClassMethods = $this->getParentClassMethods();

        foreach ($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (isset($this->config['excludeParentMethods']) && $this->config['excludeParentMethods'] === true) {
                if (isset($parentClassMethods[$method->getName()])) {
                    continue;
                }
            }
            $docblock = new DocBlock($method);
            $data = [
                'shortDescription' => $docblock->getShortDescription(),
                'longDescription' => $docblock->getLongDescription(),
                'argumentsList' => $this->retriveParams($docblock->getTagsByName('param')),
                'argumentsDescription' => $this->retriveParamsDescription($docblock->getTagsByName('param')),
                'returnValue' => $this->retriveReturnValue($docblock->getTagsByName('return')),
                'visibility' =>  join(
                    '',
                    [
                        $method->isFinal() ? 'final ' : '',
                        'public',
                        $method->isStatic() ? ' static' : '',
                    ]
                ),
            ];
            $methods[$method->getName()] = (object)$data;

        }

        return $methods;
    }

    private function getParentClassMethods()
    {
        $methods = [];
        $parentClass = $this->reflection->getParentClass();
        if ($parentClass) {
            foreach ($parentClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methods[$method->getName()] = $method;
            }
        }
        return $methods;
    }

    private function retriveReturnValue(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = (object)[
                'desc' => $param->getDescription(),
                'type' => $param->getTypes(),
            ];
        }
        return $data;
    }

    private function retriveParams(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = sprintf("%s %s", join('|', $param->getTypes()), $param->getVariableName());
        }
        return $data;
    }

    private function retriveParamsDescription(array $params)
    {
        $data = [];
        foreach ($params as $param) {
            $data[] = (object)[
                'name' => $param->getVariableName(),
                'desc' => $param->getDescription(),
                'type' => $param->getTypes(),
            ];
        }
        return $data;
    }
}
