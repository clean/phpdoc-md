<?php namespace Clean\PhpDocMd\Markdown\GitHub;

use Clean\PhpDocMd\Markdown\ClassInfo as AbstractClassInfo;

class ClassInfo extends AbstractClassInfo
{
    public function getFormatName()
    {
        return 'github';
    }
}
