-- Permite registrar historial de primer traslado (hallazgo aprobado sin hoja de vida aún).
ALTER TABLE rescate.animal_histories
    ALTER COLUMN animal_file_id DROP NOT NULL;
