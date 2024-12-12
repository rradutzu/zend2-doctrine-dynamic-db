<?php

namespace DoctrineDynamicDb\Service;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;

class Connection extends \Doctrine\DBAL\Connection
{
    private string $clientDatabase = '';

    public function __construct(array $params, Driver $driver, Configuration $config = null,
                                EventManager $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * Gets the name of the database this Connection is pointing to.
     *
     * @return string
     */
    public function getDatabase()
    {
        if (!empty($this->getClientDatabase())) {
            return $this->getClientDatabase();
        }
        return parent::getDatabase();
    }

    public function getClientDatabase(): string
    {
        return $this->clientDatabase;
    }

    public function setClientDatabase(string $clientDatabase): void
    {
        $this->clientDatabase = $clientDatabase;
    }




}