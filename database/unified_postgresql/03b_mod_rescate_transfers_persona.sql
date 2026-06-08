-- Alinear transfers con persona_id (eliminar rescatista_id legado del esquema unificado)
SET search_path TO rescate, public;

DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'rescate' AND table_name = 'transfers' AND column_name = 'rescatista_id'
    ) THEN
        UPDATE rescate.transfers t
        SET persona_id = COALESCE(t.persona_id, r.persona_id)
        FROM rescate.rescuers r
        WHERE r.id = t.rescatista_id AND t.persona_id IS NULL;

        ALTER TABLE rescate.transfers DROP CONSTRAINT IF EXISTS transfers_rescatista_id_fkey;
        ALTER TABLE rescate.transfers DROP COLUMN IF EXISTS rescatista_id;
    END IF;

    IF EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'rescate' AND table_name = 'transfers' AND column_name = 'persona_id'
    ) THEN
        ALTER TABLE rescate.transfers ALTER COLUMN persona_id SET NOT NULL;
    END IF;
END $$;
