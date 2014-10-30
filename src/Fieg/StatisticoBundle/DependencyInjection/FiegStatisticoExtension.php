<?php

namespace Fieg\StatisticoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FiegStatisticoExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($redisClient = $config['driver']['redis']['client']) {
            if ('@' === substr($redisClient, 0, 1)) {
                $redisClient = substr($redisClient, 1);
            }

            if ($definition = $container->getDefinition('statistico.driver.redis')) {
                $definition->replaceArgument(0, new Reference($redisClient));
            }
        }
    }
}
