SET @foto_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'motocicletas'
      AND COLUMN_NAME = 'foto'
);

SET @foto_sql := IF(
    @foto_exists = 0,
    'ALTER TABLE motocicletas ADD COLUMN foto VARCHAR(255) NULL AFTER unidad_asignada',
    'SELECT 1'
);

PREPARE foto_statement FROM @foto_sql;
EXECUTE foto_statement;
DEALLOCATE PREPARE foto_statement;
