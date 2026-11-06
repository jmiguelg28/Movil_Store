-- ================================================================
-- MOVILL STORE / MIGRACIÓN ADITIVA Y REEJECUTABLE
-- Para bases de datos existentes con imagen/descripcion ausentes.
-- 1. Haz una copia de seguridad antes de ejecutar.
-- 2. Selecciona completa esta consulta en MySQL Workbench y ejecútala.
-- 3. No elimina ni sobrescribe ningún dato existente.
-- Requiere permiso ALTER TABLE y acceso a information_schema.
-- ================================================================
USE tienda_electronica;
SET @exists_img := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'imagen'
);
SET @sql_img := IF(@exists_img = 0,
  'ALTER TABLE `producto` ADD COLUMN `imagen` VARCHAR(255) NULL', 'SELECT ''imagen ya existe''');
PREPARE movill_img FROM @sql_img;
EXECUTE movill_img;
DEALLOCATE PREPARE movill_img;

SET @exists_desc := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'producto' AND COLUMN_NAME = 'descripcion'
);
SET @sql_desc := IF(@exists_desc = 0,
  'ALTER TABLE `producto` ADD COLUMN `descripcion` TEXT NULL', 'SELECT ''descripcion ya existe''');
PREPARE movill_desc FROM @sql_desc;
EXECUTE movill_desc;
DEALLOCATE PREPARE movill_desc;
