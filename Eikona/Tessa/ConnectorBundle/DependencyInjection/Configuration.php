<?php

namespace Eikona\Tessa\ConnectorBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pim_eikona_tessa_connector');

        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'base_url' => ['value' => null],
                'ui_url' => ['value' => null],
                'use_http_internally' => ['value' => false, 'type' => 'bool'],
                'username' => ['value' => null],
                'api_key' => ['value' => null],
                'system_identifier' => ['value' => null],
                'disable_asset_editing_in_akeneo_ui' => ['value' => false, 'type' => 'bool'],
                'enable_reference_entity_tessa_main_image' => ['value' => false, 'type' => 'bool'],
            ]
        );

        return $treeBuilder;
    }
}
