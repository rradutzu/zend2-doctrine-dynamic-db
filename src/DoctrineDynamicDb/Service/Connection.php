<?php

namespace DoctrineDynamicDb\Service;

class Connection extends \Doctrine\DBAL\Connection
{
    private string $newDbName;

    public function __construct(\Doctrine\DBAL\Connection $connection, string $newDbName)
    {
        $this->newDbName = $newDbName;
        parent::__construct($connection->getParams(), $connection->getDriver(), $connection->getConfiguration(), $connection->getEventManager());
    }

    /**
     * Gets the name of the database this Connection is pointing to.
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->newDbName;
    }
}