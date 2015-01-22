<?php

namespace Fullpipe\EmailTemplateBundle\Exception;

class TemplateNotExistsException extends \RuntimeException
{
    public function __construct($name)
    {
        parent::__construct(sprintf("Template with name '%s' does not exists.", $name));
    }
}