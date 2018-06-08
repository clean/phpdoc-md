# Clean\PhpDocMd\Example\BasicClass  

Example_Class is a sample class for demonstrating PHPDoc

Example_Class is a class that has no real actual code, but merely
exists to help provide people with an understanding as to how the
various PHPDoc tags are used.  





## Methods

| Name | Description |
|------|-------------|
|[__construct](#basicclass__construct)|Constructs an object|
|[addValues](#basicclassaddvalues)|Adds two arguments|
|[one](#basicclassone)|Returns one|




### BasicClass::__construct  

**Description**

```php
public __construct (array $options)
```

Constructs an object 

 

**Parameters**

* `(array) $options`
: options  

**Return Values**

`void`




<hr />


### BasicClass::addValues  

**Description**

```php
public addValues (float $one, float $two)
```

Adds two arguments 

 

**Parameters**

* `(float) $one`
: First argument  
* `(float) $two`
: Second argument  

**Return Values**

`float`




**Throws Exceptions**


`\InvalidArgumentException`
> Thrown when a param is invalid

`\RuntimeException`
> Thrown when something happens at runtime

<hr />


### BasicClass::one  

**Description**

```php
final public static one (void)
```

Returns one 

That is long description for one method  
written in more then one line 

**Parameters**

`This function has no parameters.`

**Return Values**

`integer`




<hr />

