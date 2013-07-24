<?php

namespace RedCode\InstagramFeedBundle\DependencyInjection;

use RedCode\InstagramFeedBundle\Service\ApproveRule\ApproveRuleManager;
use RedCode\InstagramFeedBundle\Service\ApproveRule\DateRangeApproveRule;
use RedCode\InstagramFeedBundle\Service\ApproveRule\UsernameApproveRule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RedCodeInstagramFeedExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('redcode.instagram.tag.class', $config['tag_class']);
        $container->setParameter('redcode.instagram.image.class', $config['image_class']);
        $container->setParameter('redcode.instagram.app.client_id', $config['client_id']);
        $container->setParameter('redcode.instagram.app.load.start_from', $config['start_from']);
        $container->setParameter('redcode.instagram.app.page_items', $config['page_items']);

        $serviceId = 'redcode.instagram.rule.approver';
        $container
            ->setDefinition($serviceId, new DefinitionDecorator('redcode.instagram.rule_approver'))
            ->replaceArgument(0, $config['approve_rules'])

        ;
    }

    public function getNamespace()
    {
        return 'redcode_instagram';
    }
}
