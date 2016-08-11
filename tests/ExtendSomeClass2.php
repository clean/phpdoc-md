<?php namespace Clean\PhpDocMd\Tests;

class ExtendSomeClass2 extends ExtendSomeClass
{
	/**
	* A summary informing the user what the associated element does.
	*
	* A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
	* and to provide some background information or textual references.
	*
	* @param string $firstArgument With a *description* of this argument, these may also
	*    span multiple lines.
	* @param string $secondArgument With a *description* of this argument, these may also
	*    span multiple lines.
	*
	* @return void
	*/
    public function public2Method($firstArgument, $secondArgument)
    {
    }
}
