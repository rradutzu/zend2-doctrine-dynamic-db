# Doctrine Dynamic Db Adapter

Zend2 Doctrine Module that allows defining connections using dynamic database names

#Usage
----------------

1. Include 'DoctrineDynamicDb' in your modules.config.php file
----------------

2. Add to the used connection the 'dbNameFactory' param.:
----------------
    return array(
        //...
        'doctrine' => array(
            'connection' => array(
                //...
                'orm_dynamic' => array(
                    'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                        'params' => array(
                        'host'     => 'localhost',
                        'port'     => '3306',
                        'user'     => 'root',
                        'password' => 'kotor3',
                        'dbname'   => '%',

                        // this needs to return instance of DoctrineMultiDbAdapter\Client\ClientInterface,
                        // a custom object or a string which represents the db name
                        'dbNameFactory' => 'ClientFactory',
                        // optional | name of the method used to retrieve the db name if dbNameFactory
                        // returns a custom object
                        'dbNameFactoryMethod' => 'getClientDb'
                    )
                )
            ),
            'dynamic_entitymanager' => array(
                // need to add the connection to the new entity manager
                'orm_dynamic' => array()
            )
        ),
        //...
    );
    
3.Get the entity manager for the dynamic connection using:
----------------
    $em = $serviceLocator->get('doctrine.dynamic_entitymanager.##connection_name##');


