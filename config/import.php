<?php

return [
    // <AddressObjectType LEVEL="7" SOCRNAME="Территория" SCNAME="тер" KOD_T_ST="726" />
    'address_object_levels' => [
        'table_name' => 'address_object_levels',
        'node_name' => 'AddressObjectType',
        'xml_key' => 'LEVEL',
        'database_key' => 'id',
        'fields' => [
            'LEVEL' => ['name' => 'id', 'type' => 'uuid', 'primary' => 1],
            'SOCRNAME' => ['name' => 'title', 'type' => 'varchar'],
            'SCNAME' => ['name' => 'code', 'type' => 'varchar'],
        ],
    ],

    // <Object AOID="06bf1d1d-3efc-4904-9b3f-003acda465bc" AOGUID="7dfd1515-c937-4169-bc20-666f6ee3fff4" PARENTGUID="5558bfef-cb69-471d-a906-4aaf80ead113" NEXTID="3dd69d72-2382-4031-92f2-1223884fe57e" FORMALNAME="Костикова" OFFNAME="Костикова" SHORTNAME="ул" AOLEVEL="7" REGIONCODE="01" AREACODE="004" AUTOCODE="0" CITYCODE="000" CTARCODE="000" PLACECODE="024" PLANCODE="0000" STREETCODE="0004" EXTRCODE="0000" SEXTCODE="000" PLAINCODE="010040000240004" CURRSTATUS="1" ACTSTATUS="0" LIVESTATUS="0" CENTSTATUS="0" OPERSTATUS="1" IFNSFL="0105" IFNSUL="0105" TERRIFNSFL="0104" TERRIFNSUL="0104" OKATO="79222825009" OKTMO="79622425" POSTALCODE="385782" STARTDATE="1900-01-01" ENDDATE="2014-01-04" UPDATEDATE="2015-02-03" DIVTYPE="0" />
    'address_objects' => [
        'table_name' => 'address_objects',
        'node_name' => 'Object',
        'xml_key' => 'AOID',
        'database_key' => 'id',
        'fields' => [
            'AOID' => ['name' => 'id', 'type' => 'uuid', 'primary' => 1],
            'AOGUID' => ['name' => 'address_id', 'type' => 'uuid'],
            'AOLEVEL' => ['name' => 'address_level', 'type' => 'integer'],
            'PARENTGUID' => ['name' => 'parent_id', 'type' => 'uuid'],
            'FORMALNAME' => ['name' => 'title', 'type' => 'varchar'],
            'POSTALCODE' => ['name' => 'postal_code', 'type' => 'integer'],
            'SHORTNAME' => ['name' => 'prefix', 'type' => 'varchar'],
            'REGIONCODE' => ['name' => 'region', 'type' => 'integer'],
        ],
        'filters' => [
            ['field' => 'ACTSTATUS', 'type' => 'eq', 'value' => 1],
        ],
    ],

    // <House HOUSEID="96a60057-523d-4d66-b610-0000000608b3" HOUSEGUID="96a60057-523d-4d66-b610-0000000608b3" AOGUID="600f3c31-6682-48d5-91d3-ab72cc644f76" HOUSENUM="74" STRSTATUS="0" ESTSTATUS="2" STATSTATUS="0" IFNSFL="3022" IFNSUL="3022" OKATO="12205820001" OKTMO="12605420" POSTALCODE="416525" STARTDATE="1900-01-01" ENDDATE="2079-06-06" UPDATEDATE="2011-12-28" COUNTER="87" DIVTYPE="0" />
    'houses' => [
        'table_name' => 'houses',
        'node_name' => 'House',
        'xml_key' => 'HOUSEID',
        'database_key' => 'id',
        'fields' => [
            'HOUSEID' => ['name' => 'id', 'type' => 'uuid', 'primary' => 1],
            'HOUSEGUID' => ['name' => 'house_id', 'type' => 'uuid'],
            'AOGUID' => ['name' => 'address_id', 'type' => 'uuid'],
            'HOUSENUM' => ['name' => 'number', 'type' => 'varchar'],
            'BUILDNUM' => ['name' => 'building', 'type' => 'varchar'],
            'STRUCNUM' => ['name' => 'structure', 'type' => 'varchar'],
        ],
    ],

    // <Room ROOMID="8100b1de-4707-42ae-909c-b1cf6b4a210e" ROOMGUID="8100b1de-4707-42ae-909c-b1cf6b4a210e" HOUSEGUID="8425ff55-ec42-466a-a993-de4e5faaba3b" REGIONCODE="77" FLATNUMBER="101" FLATTYPE="2" CADNUM="77:07:0006002:1878" POSTALCODE="121170" UPDATEDATE="2017-07-02" OPERSTATUS="10" STARTDATE="1900-01-01" ENDDATE="2079-06-06" LIVESTATUS="1" />
    'rooms' => [
        'table_name' => 'rooms',
        'node_name' => 'Room',
        'xml_key' => 'ROOMID',
        'database_key' => 'id',
        'fields' => [
            'ROOMGUID' => ['name' => 'id', 'type' => 'uuid', 'primary' => 1],
            'HOUSEGUID' => ['name' => 'house_id', 'type' => 'uuid'],
            'CADNUM' => ['name' => 'cadastr_number', 'type' => 'varchar'],
            'POSTALCODE' => ['name' => 'postal_code', 'type' => 'integer'],
            'FLATNUMBER' => ['name' => 'flat_number', 'type' => 'varchar'],
        ],
    ],
];
