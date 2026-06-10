<?php

declare(strict_types=1);

namespace Sylament\Support;

use Sylament\Support\Services\Invoker;
use Sylament\Support\Services\PropertyAccessor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class SylamentSupportBundle extends SylamentBundle
{
    /**
     * @param array<array-key, string> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $this->configureServices($configurator);
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $this->prependAssetMapper($container);
    }

    private function configureServices(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set('sylament_support.invoker', Invoker::class)
            ->args([
                '$container' => service('service_container')
            ])
            ->alias(Invoker::class, 'sylament_support.invoker');

        $services
            ->set('sylament_support.property_accessor', PropertyAccessor::class)
            ->alias(PropertyAccessor::class, 'sylament_support.property_accessor');
    }

    private function prependAssetMapper(ContainerBuilder $container): void
    {
        if (!$this->isAssetMapperAvailable($container)) {
            return;
        }

        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__ . '/../assets/dist' => '@sylament/support',
                ],
            ],
        ]);
    }
}
