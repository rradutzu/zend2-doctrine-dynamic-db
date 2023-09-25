<?php

namespace DoctrineDynamicDb\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineORMModule\Options\EntityManager as DoctrineORMModuleEntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use DoctrineDynamicDb\Client\ClientInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class DynamicEntityManagerFactory
 * @package DoctrineDynamicDb\Service
 */
class DynamicEntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     *
     * @return EntityManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EntityManager
    {
        /* @var $options \DoctrineORMModule\Options\EntityManager */
        $options = $this->getOptions($container, 'entitymanager');

        $connectionName = $options->getConnection();
        $configurationName = $options->getConfiguration();
        $globalConfig = $container->get('Configuration');

        if (empty($globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactory'])) {
            throw new ServiceNotCreatedException('Option dbNameFactory not found or empty');
        }

        $clientConf = $container->get($globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactory']);

        if (is_string($clientConf)) {
            $dbName = $clientConf;
        } else if (is_object($clientConf) && $clientConf instanceof ClientInterface) {
            $dbName = $clientConf->getDbName();
        } else if (is_object($clientConf)) {
            // custom object
            if (empty($globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactoryMethod'])) {
                throw new ServiceNotCreatedException('Option dbNameFactoryMethod not found or empty');
            }
            $clientObjectGetDbMethod = $globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactoryMethod'];
            $dbName = $clientConf->{$clientObjectGetDbMethod}();
        }

        if (empty($dbName)) {
            throw new ServiceNotCreatedException('Empty client db name!');
        }

        // we need to reset the connection parameters here
        $globalConfig['doctrine']['connection'][$this->name]['params']['dbname'] = $dbName;
        $isAllowOverride = $container->getAllowOverride();
        $container->setAllowOverride(true);
        $container->setService('config', $globalConfig);
        $container->setService('Configuration', $globalConfig);
        $container->setService('configuration', $globalConfig);
        $container->setService('Config', $globalConfig);
        $container->setAllowOverride($isAllowOverride);

        $connection = $container->get($connectionName);
        $config = $container->get($configurationName);

        // initializing the resolver
        // @todo should actually attach it to a fetched event manager here, and not
        //       rely on its factory code
        $container->get($options->getEntityResolver());
        return new EntityManager($connection, $config);
    }

    /**
     * {@inheritDoc}
     * @return EntityManager
     * @throws ContainerExceptionInterface
     */
    public function createService(ServiceLocatorInterface $container): EntityManager
    {
        return $this($container, EntityManager::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass(): string
    {
        return DoctrineORMModuleEntityManager::class;
    }
}
