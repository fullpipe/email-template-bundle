<?php

namespace Fullpipe\EmailTemplateBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class FullpipeEmailTemplateExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $config['utm'] = isset($config['utm']) ? $config['utm'] : array();
        $config['from'] = $this->prepareAddresses($config['from']);
        $config['reply_to'] = $this->prepareAddresses($config['reply_to']);

        foreach ($config['templates'] as $templateName => $templateConfig) {
            if (isset($templateConfig['from']['email'])) {
                $templateConfig['from'] = $this->prepareAddresses($templateConfig['from']);
            }

            if (isset($templateConfig['reply_to']['email'])) {
                $templateConfig['reply_to'] = $this->prepareAddresses($templateConfig['reply_to']);
            }

            $config['templates'][$templateName] = array_merge(
                array(
                    'utm' => $config['utm'],
                    'host' => $config['host'],
                    'from' => $config['from'],
                    'reply_to' => $config['reply_to'],
                ),
                $templateConfig
            );
        }

        $container->setParameter('fullpipe_email_template.defaults.utm', $config['utm']);
        $container->setParameter('fullpipe_email_template.defaults.host', $config['host']);
        $container->setParameter('fullpipe_email_template.defaults.template', $config['templates']['default']);
        $container->setParameter('fullpipe_email_template.templates', $config['templates']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * [prepareAddresses description]
     * @param  array $emailConfig
     * @return array ['email' => 'name'] or ['email'] if name is null
     */
    private function prepareAddresses($emailConfig)
    {
        return
            null === $emailConfig['name']
            ? (array) $emailConfig['email']
            : array($emailConfig['email'] => $emailConfig['name']);
    }
}
