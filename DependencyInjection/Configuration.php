<?php

namespace WEBMI\TrackingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('webmi_tracking');
        
        $supportedManagerTypes = array('orm');
        $rootNode 
            ->children()
                ->arrayNode('domains')
                    ->prototype('scalar')->defaultValue('*')->end()
                ->end()
                ->arrayNode('ignored_domains')
                    ->prototype('scalar')->defaultValue(array())->end()
                ->end()
                ->scalarNode('tracking_class')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
        /*$rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('manager_type')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedManagerTypes)
                        ->thenInvalid('The manager type %s is not supported. Please choose one of '.json_encode($supportedManagerTypes))
                    ->end()
                ->end()
                ->scalarNode('tracking_manager')->defaultValue('webmi_tracking.tracking_manager.class.default')->end()
                ->arrayNode('twig_extension')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('action')->defaultValue('_track_action')->end()
                    ->end()
                ->end()
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('security')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('login')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('success')->defaultValue('webmi_tracking.security.interactive_login_listener.success.default')->end()
                                        ->scalarNode('failure')->defaultValue('webmi_tracking.security.interactive_login_listener.failure.default')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('request')->defaultValue('webmi_tracking.request_listener.default')->end()
                        ->scalarNode('controller')->defaultValue('webmi_tracking.controller_listener.default')->end()
                    ->end()
                ->end()
            ->end();*/
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
