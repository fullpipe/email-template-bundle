<?php

namespace Fullpipe\EmailTemplateBundle\Factory;

interface MessageFactoryInterface
{
    /**
     * Create \Swift_Message
     * @param  string $templateName template name in fullpipe_email_template.templates
     * @param  array  $context      template context
     * @return \Swift_Message
     */
    public function create($templateName, array $context);
}