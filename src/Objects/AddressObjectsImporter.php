<?php

namespace Objects;

use Grace\DBAL\ConnectionAbstract\ConnectionInterface;

/**
 * Class AddressObjectsImporter
 * @package Objects
 */
class AddressObjectsImporter extends \Importer
{

    public function __construct(ConnectionInterface $db, $table, array $fields)
    {
        parent::__construct($db, $table, $fields, false);
    }

    /**
     *
     */
    public function modifyDataAfterImport()
    {
        \RawDataHelper::cleanAddressObjects($this->db);
    }

    protected $rowsPerInsert = 100000;

    /**
     * @param int $limit
     * @return int
     */
    public function setRowsLimit($limit = 10000)
    {
        return $this->rowsPerInsert = $limit;
    }

    /**
     * @return int
     */
    public function getRowsLimit()
    {
        return $this->rowsPerInsert;
    }
}
