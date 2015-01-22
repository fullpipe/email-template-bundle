<?php

namespace Fullpipe\EmailTemplateBundle\Exception;

class TemplatePartRequiredException extends \RuntimeException
{
    public function __construct($partName, $template)
    {
        parent::__construct(sprintf("Add '%s' block to you '%s'", $partName, $template));
    }
}