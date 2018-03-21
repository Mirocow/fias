FIAS
====

* https://sohabr.net/habr/post/316314/
* https://sohabr.net/habr/post/316380/
* https://sohabr.net/habr/post/316622/

Автодополнение физических адресов по базе ФИАС.

## Инициалиация базы данных

Для инициализации необходимо запустить `init.php`. Поддерживаются 3 режима работы:

1. `php cli/init.php` — скачает с сайта ФИАСа последнюю версию базы, распакует и импортирует;
2. `php cli/init.php /path/to/archive.rar` — распакует и импортирует архив;
3. `php cli/init.php /path/to/fias_directory` — импортирует уже распакованный архив.

## API

### `/api/complete` — дополнение адреса

Пример запроса:

    http://fias.loc/api/complete?pattern=Невск&limit=20

    Ответ:
    {
        "items": [
            {"title": "г Москва, пр Невский", "is_complete": false, "tags": ["address"]},
            {"title": "г Москва, Невское урочище", "is_complete": false, "tags": ["address"]},
            {"title": "Невский вокзал", "is_complete": true, "tags": ["place", "railway"]}
        ]
    }

GET-параметры:

* `pattern` — дополняемый адрес;
* `limit` — максимальное количество вариантов дополнения в ответе (не более 50, см. `config/config.ini`);
* `regions` — массив номеров регионов для ограничения поиска адресов (см. `database/02_system_data.sql`);
* `max_address_level` — максимальная детализация адреса.

Максимальная детализация влияет на состав дополняемых вариантов (см. ниже).

Поля ответа:

* `items` — массив вариантов дополнения адреса;
    * `title` — текст варианта дополнения;
    * `is_complete` — `true` для адресов, которым не нужно дальнейшее дополнение (набран точный адрес, либо достигнута максимальная детализация адреса);
    * `tags` — присущие варианту ответа свойства (см. раздел теги).

Параметр `is_complete` помогает отличить точные адреса от промежуточных вариантов дополнения.
Например, если на Невском проспекте есть дом 11, то
при дополнении строки "Санкт-Петербург" для варианта "Санкт-Петербург, Невский проспект" `is_complete=false`,
а дополнении строки "Санкт-Петербург, Невский проспект" для варианта "Санкт-Петербург, Невский проспект 11" `is_complete=true`.
Параметр `is_complete` не учитывает параметры детализации.

Примеры запросов с ограничением детализации:

    http://fias.loc/api/complete?pattern=Москва, Невский пр.&limit=20

    В ответе будут все варианты вплоть до номеров домов:
    {
        "items": [
            {"title": "г Москва, пр Невский, 10", "is_complete": true, "tags": ["address"]},
            {"title": "г Москва, пр Невский, 11", "is_complete": true, "tags": ["address"]}
        ]
    }


    http://fias.loc/api/complete?pattern=Мос&limit=20&max_address_level=region

    В ответе будут только регионы без дальнейшей детализации:
    {
        "items": [
            {"title": "г Москва", "is_complete": true, "tags": ["address"]},
            {"title": "обл Московская", "is_complete": true, "tags": ["address"]}
        ]
    }


### `/api/validate` — валидация элемента

Пример запроса:

    http://fias.loc/api/validate?pattern=Москва, Невский пр.

    Ответ:
    {
        "items": [
            {
                "is_complete": false,
                "tags": ["address"]
            },
            {
                "is_complete": true,
                "tags": ["place", "railway"]
            }
        ]
    }

GET-параметры:

* `pattern` — проверяемый адрес.

Поля ответа:

* `items` — массив вариантов корректных объектов;
    * `is_complete` — `true` для точного адреса (вместе с домом, корпусом и т.п.);
    * `tags` — присущие варианту ответа свойства (см. раздел теги):


### `/api/postal_code_location` — получение адреса по почтовому индексу

Пример запроса:

    http://fias.loc/api/postal_code_location?postal_code=198504

    Ответ:
    {
        "address_parts": [
            {"title": "г Санкт-Петербург", "address_level": "region"},
            {"title": "р-н Петродворцовый", "address_level": "city_district"}
        ]
    }

GET-параметры:

* `postal_code` — почтовый индекс.

Поля ответа:

* `address_parts` — массив частей адреса по уровням детализации;
    * `title` — название;
    * `address_level` — уровень детализации (район, город и т.п.).

Если соединить по порядку `title` всех частей адреса в строку,
получится общий префикс для всех адресов по указанному почтовому индексу.

### `/api/address_postal_code` — получение почтового индекса по адресу

Пример запроса:

    http://fias.loc/api/address_postal_code?address=обл Псковская, р-н Новосокольнический, д Мошино

    Ответ:
    {
        "postal_code": 182200
    }

GET-параметры:

* `address` — адрес.

Поля ответа:

* `postal_code` — почтовый индекс или `null`, если индекс не найден.


### Уровни детализации частей адреса

1. `region` — регион: Санкт-Петербург, Московская область, Хабаровский край;
2. `area` — округ: пока данные отсутствуют, заложено для дальнейшей совместимости с ФИАС, когда ФИАС перенесет часть элементов из region;
3. `area_district` — район округа/региона: Волжский район, Ломоносовский район, Гатчинский район;
4. `city` — город: Петергоф, Сосновый бор, Пушкин;
5. `city_district` — район города: микрорайон № 13, Кировский район, Центральный район;
6. `settlement` — населенный пункт: поселок Парголово, станция Разлив, поселок Металлострой;
7. `street` — улица: проспект Косыгина, улица Ярославская, проспект Художников;
8. `territory` — дополнительная территория: Рябинушка снт (садовое некоммерческое товарищество), Победа гск (гаражно-строительный кооператив);
9. `sub_territory` — часть дополнительной территории: Садовая улица, 7-я линия;
10. `building` — конкретный дом (максимальная детализация).

### Теги

* `"address"` — текст найден в ФИАС;
* `"place"` — текст найден в списке places (аэропорты, вокзалы, порты и т.д.);
* `"airport"` — аэропорт;
* `"railway_station"` — вокзал;
* `"bus_terminal"` — автовокзал;
* `"port"` — порт;
* `"airport_terminal"` — терминал аэропорта;
* `"riverside_station"` — речной вокзал.


### Выбор формата

Для указания формата необходимо добавить его к названию ресурса:

* `.json` для JSON (по умолчанию)
* `.jsonp` для JSONP. Для JSONP требуется дополнительный GET параметр callback.

Пример запроса:

```
    http://fias.loc/api/complete.jsonp?pattern=Невск&limit=20&callback=someFunction

    Ответ:
    someFunction(
        {
            "items": [
                {"title": "г Москва, пр Невский", "is_complete": false, "tags": ["address"]},
                {"title": "г Москва, Невское урочище", "is_complete": false, "tags": ["address"]},
                {"title": "Невский вокзал", "is_complete": true, "tags": ["place", "railway"]}
            ]
        }
    )
```

## Парсер

```
77:07:0006002:1851 Москва, р-н Дорогомилово, ул 1812 Года, д 1, кв 1 Помещение 61.9 кв.м
Кадастровый номер: 77:07:0006002:1007
Москва , улица 1812 года, д. 1
https://kadastrmap.ru/?kad_no=77:07:0006002:1851
```

* https://vipiska-egrn.ru/reestr/0c5b2444-70a0-4932-980c-b4dc0d3f02b5/	г  Москва
* https://vipiska-egrn.ru/reestr/519f7189-633d-4109-9fd6-a10e8d9131a8/	улица 1812 года
* https://vipiska-egrn.ru/reestr/8425ff55-ec42-466a-a993-de4e5faaba3b/	д. 1

### Объекты и таблицы

#### address_objects

AS_ADDROBJ_20180114_2b33ee23-fcd4-4fac-92e0-b08577b7bb21.XML
```
<Object AOID="5c8b06f1-518e-496e-b683-7bf917e0d70b" AOGUID="0c5b2444-70a0-4932-980c-b4dc0d3f02b5" FORMALNAME="Москва" OFFNAME="Москва" SHORTNAME="г" AOLEVEL="1" REGIONCODE="77" AREACODE="000" AUTOCODE="0" CITYCODE="000" CTARCODE="000" PLACECODE="000" PLANCODE="0000" STREETCODE="0000" EXTRCODE="0000" SEXTCODE="000" PLAINCODE="77000000000" CODE="7700000000000" CURRSTATUS="0" ACTSTATUS="1" LIVESTATUS="1" CENTSTATUS="0" OPERSTATUS="1" IFNSFL="7700" IFNSUL="7700" OKATO="45000000000" STARTDATE="1900-01-01" ENDDATE="2079-06-06" UPDATEDATE="2017-04-17" DIVTYPE="0" />
<Object AOID="60392dbf-fac0-4e5b-a93b-af20b0b8d1b7" AOGUID="519f7189-633d-4109-9fd6-a10e8d9131a8" PARENTGUID="0c5b2444-70a0-4932-980c-b4dc0d3f02b5" NEXTID="88e4ecbe-5fda-4092-bda5-96c17852649f" FORMALNAME="1812 года" OFFNAME="1812 года" SHORTNAME="ул" AOLEVEL="7" REGIONCODE="77" AREACODE="000" AUTOCODE="0" CITYCODE="000" CTARCODE="000" PLACECODE="000" PLANCODE="0000" STREETCODE="0701" EXTRCODE="0000" SEXTCODE="000" PLAINCODE="770000000000701" CURRSTATUS="1" ACTSTATUS="0" LIVESTATUS="0" CENTSTATUS="0" OPERSTATUS="1" IFNSFL="7730" IFNSUL="7730" OKATO="45268554000" OKTMO="45318000" POSTALCODE="121170" STARTDATE="1900-01-01" ENDDATE="2016-06-09" UPDATEDATE="2016-06-10" NORMDOC="765089d6-4907-4094-b12a-462cf8f345d9" DIVTYPE="0" />
<Object AOID="88e4ecbe-5fda-4092-bda5-96c17852649f" AOGUID="519f7189-633d-4109-9fd6-a10e8d9131a8" PARENTGUID="0c5b2444-70a0-4932-980c-b4dc0d3f02b5" PREVID="60392dbf-fac0-4e5b-a93b-af20b0b8d1b7" FORMALNAME="1812 года" OFFNAME="1812 года" SHORTNAME="ул" AOLEVEL="7" REGIONCODE="77" AREACODE="000" AUTOCODE="0" CITYCODE="000" CTARCODE="000" PLACECODE="000" PLANCODE="0000" STREETCODE="0701" EXTRCODE="0000" SEXTCODE="000" PLAINCODE="770000000000701" CODE="77000000000070100" CURRSTATUS="0" ACTSTATUS="1" LIVESTATUS="1" CENTSTATUS="0" OPERSTATUS="20" IFNSFL="7730" IFNSUL="7730" OKATO="45268554000" OKTMO="45318000" POSTALCODE="121170" STARTDATE="2016-06-09" ENDDATE="2079-06-06" UPDATEDATE="2016-06-09" NORMDOC="b26f57e6-8e66-471b-811d-74831271b851" DIVTYPE="0" />
```

* CURRSTATUS = 0
* ACTSTATUS = 1
* NEXTID = ''

#### houses

* address_objects.AOGUID = houses.AOGUID

AS_HOUSE_20180114_d5112d99-64d5-46ef-8a16-345c35c841a8.XML
```
<House HOUSEID="8425ff55-ec42-466a-a993-de4e5faaba3b" HOUSEGUID="8425ff55-ec42-466a-a993-de4e5faaba3b" AOGUID="519f7189-633d-4109-9fd6-a10e8d9131a8" HOUSENUM="1" STRSTATUS="0" ESTSTATUS="2" STATSTATUS="0" IFNSFL="7730" IFNSUL="7730" OKATO="45268554000" OKTMO="45318000" POSTALCODE="121170" STARTDATE="1900-01-01" ENDDATE="2079-06-06" UPDATEDATE="2012-02-27" COUNTER="1" DIVTYPE="0" />
```

* AOGUID - ID Объекта
* HOUSENUM - Номер дома

#### rooms

* houses.HOUSEID = rooms.HOUSEGUID

AS_ROOM_20180114_9fc52366-7874-4aab-9477-440734d8463c.XML
```
<Room ROOMID="150beba4-a7d8-4477-ba84-a1922a507cf5" ROOMGUID="150beba4-a7d8-4477-ba84-a1922a507cf5" HOUSEGUID="8425ff55-ec42-466a-a993-de4e5faaba3b" REGIONCODE="77" FLATNUMBER="10" FLATTYPE="2" CADNUM="77:07:0006002:1883" POSTALCODE="121170" UPDATEDATE="2017-06-28" OPERSTATUS="10" STARTDATE="1900-01-01" ENDDATE="2079-06-06" LIVESTATUS="1" />
<Room ROOMID="f5f1dc73-4e3e-4624-af90-ff6447c6e8fb" ROOMGUID="f5f1dc73-4e3e-4624-af90-ff6447c6e8fb" HOUSEGUID="8425ff55-ec42-466a-a993-de4e5faaba3b" REGIONCODE="77" FLATNUMBER="100" FLATTYPE="2" CADNUM="77:07:0006002:1877" POSTALCODE="121170" UPDATEDATE="2017-06-28" OPERSTATUS="10" STARTDATE="1900-01-01" ENDDATE="2079-06-06" LIVESTATUS="1" /
<Room ROOMID="8100b1de-4707-42ae-909c-b1cf6b4a210e" ROOMGUID="8100b1de-4707-42ae-909c-b1cf6b4a210e" HOUSEGUID="8425ff55-ec42-466a-a993-de4e5faaba3b" REGIONCODE="77" FLATNUMBER="101" FLATTYPE="2" CADNUM="77:07:0006002:1878" POSTALCODE="121170" UPDATEDATE="2017-07-02" OPERSTATUS="10" STARTDATE="1900-01-01" ENDDATE="2079-06-06" LIVESTATUS="1" />
```

* ROOMGUID -
* HOUSEGUID -
* FLATNUMBER - Номер квартиры
* CADNUM - Кадастровый номер
* LIVESTATUS -

### Описание

* AS_ADDROBJ_ = Классификатор адресообразующих элементов (край > область > город > район > улица)
* AS_HOUSE_ = Сведения по номерам домов улиц городов и населенных пунктов, номера земельных участков и т.п
* AS_NORMDOC_ =
* AS_STEAD_
* AS_ROOM_

### Ссылки

* [Адреса ФИАС в среде PostgreSQL. Часть 1](https://habrahabr.ru/post/316314/)
* [Адреса ФИАС в среде PostgreSQL. Часть 2](https://habrahabr.ru/post/316380/)
* [Адреса ФИАС в среде PostgreSQL. Часть 3](https://habrahabr.ru/post/316622/)
* [Адреса ФИАС в среде PostgreSQL. Часть 4](https://habrahabr.ru/post/316856/)