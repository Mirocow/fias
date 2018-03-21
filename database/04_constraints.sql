--ALTER TABLE public.address_objects DROP CONSTRAINT IF EXISTS address_objects_parent_id_fkey;
--ALTER TABLE address_objects
--    ADD CONSTRAINT address_objects_parent_id_fkey
--    FOREIGN KEY(parent_id) REFERENCES address_objects(address_id)
--    ON UPDATE CASCADE ON DELETE CASCADE
--    DEFERRABLE INITIALLY IMMEDIATE
--;

ALTER TABLE public.address_objects DROP CONSTRAINT IF EXISTS address_objects_address_level_fkey;
ALTER TABLE address_objects
    ADD CONSTRAINT address_objects_address_level_fkey
    FOREIGN KEY(address_level) REFERENCES address_object_levels(id)
    ON UPDATE CASCADE ON DELETE CASCADE
    DEFERRABLE INITIALLY IMMEDIATE
;

ALTER TABLE public.houses DROP CONSTRAINT IF EXISTS houses_parent_id_fkey;
ALTER TABLE houses
    ADD CONSTRAINT houses_parent_id_fkey
    FOREIGN KEY(address_id) REFERENCES address_objects(address_id)
    ON UPDATE CASCADE ON DELETE CASCADE
    DEFERRABLE INITIALLY IMMEDIATE
;

