<?php

namespace DoctrineDynamicDb\Strategy;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;

class ReplaceDynamicDbQuoteStrategy extends DefaultQuoteStrategy
{
    private string $clientDbName = '';
    private array $managedEntities;

    public function __construct(string $clientDbName, array $managedEntities) {
        $this->clientDbName = $clientDbName;
        $this->managedEntities = $managedEntities;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Table names should be computed in DBAL depending on the platform
     */
    public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
    {
        if (empty($class->table['schema']) && in_array($class->getName(), $this->managedEntities)) {
            $class->table['schema'] = $this->clientDbName;
        }

        if (!empty($class->table['schema'])) {
            $tableName = $class->table['schema'] . '.' . $class->table['name'];

            if ( ! $platform->supportsSchemas() && $platform->canEmulateSchemas()) {
                $tableName = $class->table['schema'] . '__' . $class->table['name'];
            }
        } else {
            $tableName = $class->table['name'];
        }

        return isset($class->table['quoted'])
            ? $platform->quoteIdentifier($tableName)
            : $tableName;
    }
}