<?php

namespace App;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function array_merge;
use function var_dump;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @throws Exception
     */
    public function boot(): void
    {
        parent::boot();

        if (isset($_SERVER['TZ'])) {
            date_default_timezone_set($_SERVER['TZ']);
        }
    }

    public function registerBundles(): iterable
    {
        $confDir = $this->getProjectDir() . '/config/';
        $contents = array_merge(
            require $confDir . 'common/bundles.php',
            require $confDir . $this->environment . '/bundles.php'
        );
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir() . '/config/';
        $container->addResource(new FileResource($confDir . 'common/bundles.php'));
        $container->addResource(new FileResource($confDir . $this->environment . '/bundles.php'));
//        $container->setParameter('container.dumper.inline_class_loader', true);

        $loader->load($confDir . 'common/packages/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . $this->environment . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . 'common/services/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . $this->environment . '/{services}/*' . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir() . '/config/';

        $routes->import($confDir . 'common/routes/*.yaml', 'glob');
        $routes->import($confDir . $this->environment . '/{routes}/*' . self::CONFIG_EXTS, 'glob');
    }

}
