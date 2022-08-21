<?php 
/**
 * Used by ./bin/phpdoc-md script
 */
namespace Clean\PhpDocMd;
use ReflectionClass;

/**
 * Returns class info parser object for specified format and given reflection object
 * 
 * @param string $format format 
 * @param \ReflectionClass $reflection reflection 
 * @access public
 * 
 * @return \Clean\PhpDocMd\Markdown\ClassInfo
 */
function getClassInfoParser($format, ReflectionClass $reflection) {
    switch ($format) {
        case 'stash':
        case 'bitbucket':
            $parser = new Markdown\Stash\ClassInfo($reflection);
            break;
        case 'github':
            $parser = new Markdown\GitHub\ClassInfo($reflection);
            break;
        default:
            throw new \RuntimeException(sprintf("Unknown markdown format '%s'. Only 'github' or 'bitbucket' allowed. Check your .phpdoc-md config file", $config->format));
    }
    return $parser;

}

/**
 * Returns path to destination directory for given object
 *
 * Destination path is created base based on object namespace and configuration
 * parameters
 * 
 * @param ReflectionClass $reflection reflection 
 * @param string $rootNamespace rootNamespace 
 * @param string $rootDir rootDir 
 * @access public
 * 
 * @return string
 */
function getDestinationDirectory(ReflectionClass $reflection, $rootNamespace, $rootDir)
{
    return namespaceToPath(
        rtrim(
            sprintf(
                '%s/%s',
                $rootDir,
                removeRootNamespace($reflection->getNamespaceName(), $rootNamespace)
            ),
            '/'
        )
    );
}

/**
 * Transform namespace to valid path format for current OS
 * 
 * @param string $namespace namespace 
 * @access public
 * 
 * @return string
 */
function namespaceToPath($namespace)
{
    return str_replace(
        '\\',
        DIRECTORY_SEPARATOR,
        $namespace
    );
}

/**
 * Removes root namespace from class name
 *
 * This will simply transform:
 *
 * \Clean\Example\Namespace\FooClass to \Namespace\FooClass
 *
 * when root namespace \Clean\Example given
 * 
 * @param string $namespace namespace 
 * @param string $root root 
 * @access public
 * 
 * @return string
 */
function removeRootNamespace($namespace, $root)
{
    $re = preg_replace(
        sprintf("/^%s/", addslashes($root)),
        '',
        $namespace
    );
    return ltrim($re, '\\');
}

/**
 * Output error message for the user, and exit
 * 
 * @param string $message message 
 * @param Exception $exception exception 
 * @access public
 * 
 * @return void
 */
function error($message, $exception = null) {
    printf("\e[0;31mERROR: %s\e0\n", trim($message));
    exit(1);
}

/**
 * Output info message for the user
 * 
 * @param string $message message 
 *
 * @return void
 */
function info($message, $verbose = false) {
    if ($verbose) {
        printf("INFO: %s\n", trim($message));
    }
}
