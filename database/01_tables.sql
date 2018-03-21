-- DROP TABLE IF EXISTS rooms;
CREATE TABLE IF NOT EXISTS rooms (
    id          UUID PRIMARY KEY NOT NULL,
    house_id    UUID             NOT NULL,
    flat_number VARCHAR,
    postal_code INTEGER,
    cadastr_number VARCHAR
);
COMMENT ON TABLE  rooms                 IS  'данные по квартирам';
COMMENT ON COLUMN rooms.id             IS  'идентификационный код записи';
COMMENT ON COLUMN rooms.house_id        IS  'идентификационный код дома';
COMMENT ON COLUMN rooms.flat_number     IS  'номер квартиры';
COMMENT ON COLUMN rooms.cadastr_number  IS  'кадастровый номер';

-- DROP TABLE IF EXISTS houses;
CREATE TABLE IF NOT EXISTS houses (
    id          UUID PRIMARY KEY NOT NULL,
    house_id    UUID             NOT NULL,
    address_id  UUID DEFAULT NULL,
    number      VARCHAR,
    full_number VARCHAR,
    building    VARCHAR,
    structure   VARCHAR,
    postal_code INTEGER
);
COMMENT ON TABLE  houses             IS 'данные по домам';
COMMENT ON COLUMN houses.id          IS 'идентификационный код записи';
COMMENT ON COLUMN houses.house_id    IS 'идентификационный код дома';
COMMENT ON COLUMN houses.address_id  IS 'идентификационный код адресного объекта';
COMMENT ON COLUMN houses.number      IS 'номер дома';
COMMENT ON COLUMN houses.building    IS 'корпус';
COMMENT ON COLUMN houses.structure   IS 'строение';
COMMENT ON COLUMN houses.postal_code IS 'индекс';

-- DROP TABLE IF EXISTS address_objects;
CREATE TABLE IF NOT EXISTS address_objects (
    id                 UUID PRIMARY KEY NOT NULL,
    address_id         UUID             NOT NULL,
    parent_id          UUID             DEFAULT NULL,
    level              INTEGER,
    address_level      INTEGER,
    house_count        INTEGER,
    next_address_level INTEGER,
    title              VARCHAR,
    full_title         VARCHAR,
    postal_code        INTEGER,
    region             VARCHAR,
    prefix             VARCHAR
);
COMMENT ON TABLE address_objects                     IS 'данные по адресным объектам(округам, улицам, городам)';
COMMENT ON COLUMN address_objects.id                 IS 'идентификационный код записи';
COMMENT ON COLUMN address_objects.address_id         IS 'идентификационный код адресного объекта';
COMMENT ON COLUMN address_objects.parent_id          IS 'идентификационный код родительского адресного объекта';
COMMENT ON COLUMN address_objects.level              IS 'уровень объекта по parent_id (0 для региона и далее по возрастающей';
COMMENT ON COLUMN address_objects.address_level      IS 'уровень объекта по ФИАС';
COMMENT ON COLUMN address_objects.parent_id          IS 'идентификационный код родительского адресного объекта';
COMMENT ON COLUMN address_objects.title              IS 'наименование объекта';
COMMENT ON COLUMN address_objects.full_title         IS 'полное наименование объекта';
COMMENT ON COLUMN address_objects.postal_code        IS 'индекс';
COMMENT ON COLUMN address_objects.region             IS 'регион';
COMMENT ON COLUMN address_objects.prefix             IS 'ул., пр. и так далее';
COMMENT ON COLUMN address_objects.house_count        IS 'количество домов';
COMMENT ON COLUMN address_objects.next_address_level IS 'уровень следующего дочернего объекта по ФИАС';

-- DROP TABLE IF EXISTS address_object_levels;
CREATE TABLE IF NOT EXISTS address_object_levels (
    id    INTEGER PRIMARY KEY,
    title VARCHAR
);
COMMENT ON TABLE address_object_levels        IS 'перечень уровня адресных объектов по ФИАС';
COMMENT ON COLUMN address_object_levels.id    IS 'идентификационный код записи';
COMMENT ON COLUMN address_object_levels.title IS 'описание уровня';

-- DROP TABLE IF EXISTS update_log;
CREATE TABLE IF NOT EXISTS update_log (
    id SERIAL PRIMARY KEY,
    version_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(0)
);
COMMENT ON TABLE update_log              IS 'лог обновлений';
COMMENT ON COLUMN update_log.version_id  IS 'id версии, полученной от базы ФИАС';
COMMENT ON COLUMN update_log.created_at  IS 'дата установки обновления/инициализации';

-- DROP TABLE IF EXISTS regions;
CREATE TABLE IF NOT EXISTS regions (
    number VARCHAR PRIMARY KEY,
    title VARCHAR
);
COMMENT ON TABLE regions         IS 'список регионов';
COMMENT ON COLUMN regions.number IS 'номер региона';
COMMENT ON COLUMN regions.title IS 'название региона';
