<?php namespace Clean\PhpDocMd\Tests;

/**
 * This is a class
 */
class Example
{
    /**
     * config
     *
     * @var array
     */
    public $config;

    /**
     * Constructs an object
     *
     * @param array $options options
     *
     * @return void
     */
    public function __construct(array $options)
    {
    }

    /**
     * Adds two arguments
     *
     * @param float $one First argument
     * @param float $two Second argument
     *
     * @return float
     */
    public function addValues($one, $two)
    {
        // code...
    }

    /**
     * Returns one
     *
     * That is long description for one method
     * written in more then one line
     *
     * @return integer
     */
    final public static function one()
    {
        return 1;
    }

    protected function foo()
    {
        // code...
    }

    private function boo()
    {
        // code...
    }
}
