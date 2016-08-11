<?php namespace Clean\PhpDocMd\Example;

class ExtendClass extends \ArrayIterator
{
	/**
	* A summary informing the user what the associated element does.
	*
	* A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
	* and to provide some background information or textual references.
	*
	*
	* @return void
	*/
    public function publicMethod()
    {
    }

    protected function protectedMethod()
    {
    }

    protected function privateMethod()
    {
    }
}
