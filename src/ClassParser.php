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

    public function __construct(ReflectionClass $class)
    {
        $this->reflection = $class;
    }

    public function getMethodsDetails()
    {
        $methods = [];
        foreach ($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $docblock = new DocBlock($method);
            $data = [
                'shortDescription' => $docblock->getShortDescription(),
                'longDescription' => $docblock->getLongDescription(),
                'argumentsList' => $this->retriveParams($docblock->getTagsByName('param')),
                'argumentsDescription' => $this->retriveParamsDescription($docblock->getTagsByName('param')),
                'returnValue' => $this->retriveReturnValue($docblock->getTagsByName('return')),
                'visibility' =>  join(
                    ' ',
                    [
                        $method->isFinal() ? 'final' : '',
                        'public',
                        $method->isStatic() ? 'static' : '',
                    ]
                ),
            ];
            $methods[$method->getName()] = (object)$data;

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
