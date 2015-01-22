<?php

namespace Fullpipe\EmailTemplateBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Fullpipe\EmailTemplateBundle\Exception\TemplateNotExistsException;

class FullpipeEmailTemplateExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!isset($config['templates']['default'])) {
            throw new TemplateNotExistsException('default');
        }

        $config['utm'] = isset($config['utm']) ? $config['utm'] : array();

        foreach ($config['templates'] as $templateName => $templateConfig) {
            $config['templates'][$templateName] = array_merge(
                array('utm' => $config['utm']),
                array('host' => $config['host']),
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
}