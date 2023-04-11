<?php

namespace Eikona\Tessa\ConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EikonaTessaConnectorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'pim_eikona_tessa_connector';
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controllers.yml');
        $loader->load('services.yml');
        $loader->load('converters.yml');
        $loader->load('formatters.yml');
        $loader->load('attribute_types.yml');
        $loader->load('comparators.yml');
        $loader->load('updaters.yml');
        $loader->load('factories.yml');
        $loader->load('validators.yml');
        $loader->load('normalizers.yml');
        $loader->load('query_builders.yml');
        $loader->load('datagrid/attribute_types.yml');
        $loader->load('datagrid/filters.yml');
        $loader->load('completeness_mask_generators.yml');

        if (class_exists('Akeneo\Platform\EnterpriseVersion')) {
            $loader->load('query_builders_ee.yml');
            $loader->load('presenters_ee.yml');
            $loader->load('renderers_ee.yml');
        } else {
            $loader->load('renderers_ce.yml');
        }
    }
}
