<?php

namespace DoctrineDynamicDb\Client;

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 30/11/15
 * Time: 17:50
 *
 * Main Interface that must be implemented for getting the database name
 * in order to construct the dynamic adapter
 *
 */
interface ClientInterface
{
    public function getDbName();
}
