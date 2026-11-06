-- ============================================================
--  Ajustes OPCIONALES para una base "tienda_electronica" que ya creaste
--  desde MySQL Workbench con el modelo original.
--  El sistema funciona sin ellos, pero se recomiendan:
--   1) compras_producto tenía columnas VARCHAR para números.
--   2) El modelo no tenía llaves foráneas (integridad referencial).
--  Ejecuta cada bloque por separado; si un bloque da error por datos
--  inconsistentes, corrige esos datos y vuelve a intentarlo.
-- ============================================================
USE tienda_electronica;

-- 1) Tipos correctos en compras_producto
ALTER TABLE compras_producto
  MODIFY cod_producto INT NOT NULL,
  MODIFY cantidad     INT NULL,
  MODIFY valor        DECIMAL(14,2) NULL,
  MODIFY subtotal     DECIMAL(14,2) NULL,
  MODIFY cod_material INT NULL;

-- 1b) Montos más amplios: DECIMAL(10,2) solo llega a $99.999.999,99 y una compra grande lo supera
ALTER TABLE producto        MODIFY precio   DECIMAL(12,2) NOT NULL;
ALTER TABLE ventas          MODIFY total    DECIMAL(14,2) NULL;
ALTER TABLE compras         MODIFY total    DECIMAL(14,2) NULL;
ALTER TABLE ventas_producto MODIFY subtotal DECIMAL(14,2) NULL;

-- 1c) Imagen y descripción de los productos (la tienda las usa).
--     Si no están creadas, prefiere el script idempotente 04_migracion_segura.sql.
--     NO ejecutes estos ALTER sobre columnas existentes: darían error.
ALTER TABLE producto ADD COLUMN imagen VARCHAR(255) NULL;
ALTER TABLE producto ADD COLUMN descripcion TEXT NULL;

-- 2) Llaves foráneas
ALTER TABLE clientes          ADD CONSTRAINT fk_clientes_ciudad    FOREIGN KEY (cod_ciudad)   REFERENCES ciudad (cod_ciudad);
ALTER TABLE proveedores       ADD CONSTRAINT fk_proveedores_ciudad FOREIGN KEY (cod_ciudad)   REFERENCES ciudad (cod_ciudad);
ALTER TABLE producto          ADD CONSTRAINT fk_producto_marca     FOREIGN KEY (cod_marca)    REFERENCES marca (cod_marca);
ALTER TABLE producto          ADD CONSTRAINT fk_producto_tipo      FOREIGN KEY (cod_tipo_p)   REFERENCES tipo_producto (cod_tipo_p);
ALTER TABLE producto_color    ADD CONSTRAINT fk_pc_producto        FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto);
ALTER TABLE producto_color    ADD CONSTRAINT fk_pc_color           FOREIGN KEY (cod_color)    REFERENCES color (cod_color);
ALTER TABLE producto_material ADD CONSTRAINT fk_pm_producto        FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto);
ALTER TABLE producto_material ADD CONSTRAINT fk_pm_material        FOREIGN KEY (cod_material) REFERENCES material (cod_material);
ALTER TABLE ventas            ADD CONSTRAINT fk_ventas_cliente     FOREIGN KEY (id_cliente)   REFERENCES clientes (id_cliente);
ALTER TABLE ventas            ADD CONSTRAINT fk_ventas_pago        FOREIGN KEY (cod_f_pago)   REFERENCES forma_pago (cod_f_pago);
ALTER TABLE ventas_producto   ADD CONSTRAINT fk_vp_venta           FOREIGN KEY (nro_f_venta)  REFERENCES ventas (nro_f_venta);
ALTER TABLE ventas_producto   ADD CONSTRAINT fk_vp_producto        FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto);
ALTER TABLE compras           ADD CONSTRAINT fk_compras_proveedor  FOREIGN KEY (id_proveedor) REFERENCES proveedores (id_proveedor);
ALTER TABLE compras           ADD CONSTRAINT fk_compras_pago       FOREIGN KEY (cod_f_pago)   REFERENCES forma_pago (cod_f_pago);
ALTER TABLE compras_producto  ADD CONSTRAINT fk_cp_compra          FOREIGN KEY (nro_f_compra) REFERENCES compras (nro_f_compra);
ALTER TABLE compras_producto  ADD CONSTRAINT fk_cp_producto        FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto);
