<?php

namespace Fullpipe\EmailTemplateBundle\Factory;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Html2Text\Html2Text;
use Fullpipe\EmailTemplateBundle\Exception\TemplateNotExistsException;
use Fullpipe\EmailTemplateBundle\Exception\TemplatePartRequiredException;

/**
* MessageFactory
*/
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $templateConfigs;

    public function __construct(
        \Twig_Environment $twig,
        array $templateConfigs
    ) {
        $this->twig = $twig;
        $this->templateConfigs = $templateConfigs;
    }

    /**
     * {@inheritdoc}
     */
    public function create($templateName, array $context = array())
    {
        if (!isset($this->templateConfigs[$templateName])) {
            throw new TemplateNotExistsException($templateName);
        }

        $templateConfig = $this->templateConfigs[$templateName];

        if (isset($context['utm'])) {
            $templateConfig['utm'] = array_merge(
                $templateConfig['utm'],
                $context['utm']
            );
        }

        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateConfig['template']);

        $subject = $template->renderBlock('subject', $context);
        $htmlBody = $template->renderBlock('body_html', $context);
        $textBody = $template->renderBlock('body_text', $context);

        if (empty($subject)) {
            throw new TemplatePartRequiredException('subject', $templateConfig['template']);
        }

        if (empty($htmlBody)) {
            throw new TemplatePartRequiredException('body_html', $templateConfig['template']);
        }

        $htmlBody = $this->cssToInline($htmlBody);
        $htmlBody = $this->addUtmParams($htmlBody, $templateConfig['host'], $templateConfig['utm']);

        if (empty($textBody) && $templateConfig['generate_text_version']) {
            $textBody = $this->htmlToText($htmlBody);
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setBody($htmlBody, 'text/html')
            ;

        if (!empty($textBody)) {
            $message->addPart($textBody, 'text/plain');
        }

        return $message;
    }

    /**
     * Inline all css styles
     * @param  string $html
     * @return string
     */
    private function cssToInline($html)
    {
        $inlineConverter = new CssToInlineStyles($html);
        $inlineConverter->setUseInlineStylesBlock(true);
        $inlineConverter->setStripOriginalStyleTags(true);

        return $inlineConverter->convert();
    }

    /**
     * Convert html to text
     * @param  string $html
     * @return string
     */
    private function htmlToText($html)
    {
        $converter = new Html2Text($html);

        return $converter->getText();
    }

    /**
     * Add utm_* params to our links with host
     * @param string $html
     * @param string $host
     * @param array  $utm
     */
    private function addUtmParams($html, $host, array $utm = array())
    {
        if (empty($utm)) {
            return $html;
        }

        $reg = "/(<a\s[^\>]*href=\"".preg_quote($host, '/').")(\/[\+\~\%\/\.\w\-\_]*)?\??([^\" \>]*?)(\"[^\>]*\>)/i";

        return preg_replace(
            $reg,
            '\1\2?'.http_build_query($utm).'&\3\4',
            $html
        );
    }
}
