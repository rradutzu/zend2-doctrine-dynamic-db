<?php

namespace DoctrineDynamicDb\Strategy;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;

class ReplaceDynamicDbQuoteStrategy extends DefaultQuoteStrategy
{
    private string $clientDbName;

    public function __construct(string $clientDbName) {
        $this->clientDbName = $clientDbName;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Table names should be computed in DBAL depending on the platform
     */
    public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
    {
        if (empty($class->table['schema'])) {
            $class->table['schema'] = $this->clientDbName;
        }

        $tableName = $class->table['schema'] . '.' . $class->table['name'];

        if ( ! $platform->supportsSchemas() && $platform->canEmulateSchemas()) {
            $tableName = $class->table['schema'] . '__' . $class->table['name'];
        }

        return isset($class->table['quoted'])
            ? $platform->quoteIdentifier($tableName)
            : $tableName;
    }
}