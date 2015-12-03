<?php

namespace DoctrineDynamicDb\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use DoctrineDynamicDb\Client\ClientInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DynamicEntityManagerFactory
 * @package DoctrineDynamicDb\Service
 */
class DynamicEntityManagerFactory extends AbstractFactory
{
    /**
     * {@inheritDoc}
     * @return EntityManager
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        /* @var $options \DoctrineORMModule\Options\EntityManager */
        $options = $this->getOptions($sl, 'entitymanager');

        $connectionName = $options->getConnection();
        $configurationName = $options->getConfiguration();
        $globalConfig = $sl->get('config');

        if (empty($globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactory'])) {
            throw new ServiceNotCreatedException('Option dbNameFactory not found or empty');
        }

        $clientConf = $sl->get($globalConfig['doctrine']['connection'][$this->name]['params']['dbNameFactory']);

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
        $isAllowOverride = $sl->getAllowOverride();
        $sl->setAllowOverride(true);
        $sl->setService('config', $globalConfig);
        $sl->setAllowOverride($isAllowOverride);

        $connection = $sl->get($connectionName);
        $config = $sl->get($configurationName);

        // initializing the resolver
        // @todo should actually attach it to a fetched event manager here, and not
        //       rely on its factory code
        $sl->get($options->getEntityResolver());

        return EntityManager::create($connection, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\EntityManager';
    }
}
