<?php

namespace Objects;

use Grace\DBAL\ConnectionAbstract\ConnectionInterface;

class HousesImporter extends \Importer
{

    public function __construct(ConnectionInterface $db, $table, array $fields)
    {
        parent::__construct($db, $table, $fields, false);
    }

    public function modifyDataAfterImport()
    {
        \RawDataHelper::cleanHouses($this->db, $this->table);
        \RawDataHelper::updateHousesCount($this->db);
        \RawDataHelper::updateNextAddressLevelFlag($this->db);
    }

    protected $rowsPerInsert = 100000;

    public function setRowsLimit($limit = 10000)
    {
        return $this->rowsPerInsert = $limit;
    }

    public function getRowsLimit()
    {
        return $this->rowsPerInsert;
    }
}
