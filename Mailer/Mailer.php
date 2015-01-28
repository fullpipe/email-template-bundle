<?php

namespace Fullpipe\EmailTemplateBundle\Mailer;

use Fullpipe\EmailTemplateBundle\Factory\MessageFactoryInterface;
use Fullpipe\EmailTemplateBundle\Exception\PrepareMessageException;
use Fullpipe\EmailTemplateBundle\Exception\TemplateNotExistsException;

/**
* Mailer
*/
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Fullpipe\EmailTemplateBundle\Factory\MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $templateConfigs;

    /**
     * @var array
     */
    protected $currentTemplateConfig;

    /**
     * @var \Swift_Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var array
     */
    protected $context = array();

    /**
     * @var array
     */
    protected $to;

    /**
     * @var array
     */
    protected $from;

    /**
     * @var array
     */
    protected $replyTo;

    /**
     * Constructor
     * @param \Swift_Mailer           $mailer
     * @param MessageFactoryInterface $messageFactory
     */
    public function __construct(\Swift_Mailer $mailer, MessageFactoryInterface $messageFactory, array $templateConfigs)
    {
        $this->mailer = $mailer;
        $this->messageFactory = $messageFactory;
        $this->templateConfigs = $templateConfigs;
    }

    /**
     * Prepare message
     * @param  string          $templateName
     * @param  array           $context
     * @return MailerInterface
     */
    public function prepareMessage($templateName, array $context = array())
    {
        if (!isset($this->templateConfigs[$templateName])) {
            throw new TemplateNotExistsException($templateName);
        }

        $this->currentTemplateConfig = $this->templateConfigs[$templateName];
        $this->message = $this->messageFactory->create($templateName, $context);

        return $this;
    }

    /**
     * Set to
     * @param  mixed           $addresses
     * @param  string          $name      optional
     * @return MailerInterface
     */
    public function setTo($addresses, $name = null)
    {
        $this->to = $this->prepareAddresses($addresses, $name);

        return $this;
    }

    /**
     * Get to
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set from
     * @param  mixed           $addresses
     * @param  string          $name
     * @return MailerInterface
     */
    public function setFrom($addresses, $name = null)
    {
        $this->to = $this->prepareAddresses($addresses, $name);

        return $this;
    }

    /**
     * Get From
     * @return array
     */
    public function getFrom()
    {
        if (null === $this->from) {
            return $this->currentTemplateConfig['from'];
        }

        return $this->from;
    }

    /**
     * Set ReplyTo
     * @param  mixed           $addresses
     * @param  string          $name
     * @return MailerInterface
     */
    public function setReplyTo($addresses, $name = null)
    {
        $this->replyTo = $this->prepareAddresses($addresses, $name);

        return $this;
    }

    /**
     * Get ReplyTo
     * @return array
     */
    public function getReplyTo()
    {
        if (null === $this->replyTo) {
            return $this->currentTemplateConfig['reply_to'];
        }

        return $this->replyTo;
    }

    /**
     * Send prepared message
     * @return MailerInterface
     */
    public function send()
    {
        if (null === $this->message) {
            throw new PrepareMessageException("Prepare Message first");
        }

        $this->message
            ->setTo($this->getTo())
            ->setFrom($this->getFrom())
            ->setReplyTo($this->getReplyTo())
            ;

        $this->mailer->send($this->message);

        return $this;
    }

    /**
     * Prepare email addressess
     * @param  mixed  $addresses
     * @param  string $name
     * @return array
     */
    private function prepareAddresses($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = array($addresses => $name);
        }

        return (array) $addresses;
    }
}
