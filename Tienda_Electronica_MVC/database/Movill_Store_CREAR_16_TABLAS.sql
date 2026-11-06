-- MOVILL STORE | CREACION DE 16 TABLAS EN movill_store_nueva YA CREADA
-- Importar desde phpMyAdmin con la base movill_store_nueva seleccionada.
-- Sin INSERT, DROP, DELETE, TRUNCATE ni datos de prueba.


USE `movil_store_nueva`;

CREATE TABLE IF NOT EXISTS ciudad (
  cod_ciudad INT NOT NULL AUTO_INCREMENT,
  nom_ciudad VARCHAR(45) NULL,
  PRIMARY KEY (cod_ciudad)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes (
  id_cliente INT NOT NULL AUTO_INCREMENT,
  nom_cliente VARCHAR(50) NULL,
  dir_cliente VARCHAR(100) NULL,
  tel_cliente VARCHAR(20) NULL,
  correo_cliente VARCHAR(100) NULL,
  cod_ciudad INT NULL,
  PRIMARY KEY (id_cliente),
  CONSTRAINT fk_clientes_ciudad FOREIGN KEY (cod_ciudad) REFERENCES ciudad (cod_ciudad)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS proveedores (
  id_proveedor INT NOT NULL AUTO_INCREMENT,
  nom_proveedor VARCHAR(50) NULL,
  dir_proveedor VARCHAR(100) NULL,
  tel_proveedor VARCHAR(20) NULL,
  correo_proveedor VARCHAR(100) NULL,
  cod_ciudad INT NULL,
  PRIMARY KEY (id_proveedor),
  CONSTRAINT fk_proveedores_ciudad FOREIGN KEY (cod_ciudad) REFERENCES ciudad (cod_ciudad)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS color (
  cod_color INT NOT NULL AUTO_INCREMENT,
  nom_color VARCHAR(50) NOT NULL,
  PRIMARY KEY (cod_color)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS marca (
  cod_marca INT NOT NULL AUTO_INCREMENT,
  nom_marca VARCHAR(50) NOT NULL,
  PRIMARY KEY (cod_marca)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS material (
  cod_material INT NOT NULL AUTO_INCREMENT,
  nom_material VARCHAR(50) NOT NULL,
  PRIMARY KEY (cod_material)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tipo_producto (
  cod_tipo_p INT NOT NULL AUTO_INCREMENT,
  nom_tipo_p VARCHAR(50) NOT NULL,
  PRIMARY KEY (cod_tipo_p)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS forma_pago (
  cod_f_pago INT NOT NULL AUTO_INCREMENT,
  nom_f_pago VARCHAR(50) NULL,
  PRIMARY KEY (cod_f_pago)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto (
  cod_producto INT NOT NULL AUTO_INCREMENT,
  nom_producto VARCHAR(100) NOT NULL,
  precio DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL,
  cod_marca INT NOT NULL,
  cod_tipo_p INT NOT NULL,
  imagen VARCHAR(255) NULL,
  descripcion TEXT NULL,
  PRIMARY KEY (cod_producto),
  CONSTRAINT fk_producto_marca FOREIGN KEY (cod_marca) REFERENCES marca (cod_marca),
  CONSTRAINT fk_producto_tipo  FOREIGN KEY (cod_tipo_p) REFERENCES tipo_producto (cod_tipo_p)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto_color (
  cod_producto INT NOT NULL,
  cod_color INT NOT NULL,
  PRIMARY KEY (cod_producto, cod_color),
  CONSTRAINT fk_pc_producto FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto),
  CONSTRAINT fk_pc_color    FOREIGN KEY (cod_color) REFERENCES color (cod_color)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto_material (
  cod_producto INT NOT NULL,
  cod_material INT NOT NULL,
  descripcion VARCHAR(100) NULL,
  PRIMARY KEY (cod_producto, cod_material),
  CONSTRAINT fk_pm_producto FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto),
  CONSTRAINT fk_pm_material FOREIGN KEY (cod_material) REFERENCES material (cod_material)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ventas (
  nro_f_venta INT NOT NULL AUTO_INCREMENT,
  id_cliente INT NULL,
  fecha DATE NULL,
  cod_f_pago INT NULL,
  total DECIMAL(14,2) NULL,
  PRIMARY KEY (nro_f_venta),
  CONSTRAINT fk_ventas_cliente FOREIGN KEY (id_cliente) REFERENCES clientes (id_cliente),
  CONSTRAINT fk_ventas_pago    FOREIGN KEY (cod_f_pago) REFERENCES forma_pago (cod_f_pago)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ventas_producto (
  nro_f_venta INT NOT NULL,
  cod_producto INT NOT NULL,
  cantidad INT NULL,
  subtotal DECIMAL(14,2) NULL,
  cod_material INT NULL,
  PRIMARY KEY (nro_f_venta, cod_producto),
  CONSTRAINT fk_vp_venta    FOREIGN KEY (nro_f_venta) REFERENCES ventas (nro_f_venta),
  CONSTRAINT fk_vp_producto FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS compras (
  nro_f_compra INT NOT NULL AUTO_INCREMENT,
  id_proveedor INT NULL,
  fecha DATE NULL,
  cod_f_pago INT NULL,
  total DECIMAL(14,2) NULL,
  PRIMARY KEY (nro_f_compra),
  CONSTRAINT fk_compras_proveedor FOREIGN KEY (id_proveedor) REFERENCES proveedores (id_proveedor),
  CONSTRAINT fk_compras_pago      FOREIGN KEY (cod_f_pago) REFERENCES forma_pago (cod_f_pago)
) ENGINE=InnoDB;

-- En el modelo original estas columnas eran VARCHAR; aquí se corrigen a tipos numéricos.
-- (Además los montos usan DECIMAL(14,2): con pesos colombianos DECIMAL(10,2) se queda corto en compras grandes.)
CREATE TABLE IF NOT EXISTS compras_producto (
  nro_f_compra INT NOT NULL,
  cod_producto INT NOT NULL,
  cantidad INT NULL,
  valor DECIMAL(14,2) NULL,
  subtotal DECIMAL(14,2) NULL,
  cod_material INT NULL,
  PRIMARY KEY (nro_f_compra, cod_producto),
  CONSTRAINT fk_cp_compra   FOREIGN KEY (nro_f_compra) REFERENCES compras (nro_f_compra),
  CONSTRAINT fk_cp_producto FOREIGN KEY (cod_producto) REFERENCES producto (cod_producto)
) ENGINE=InnoDB;

-- Tabla de acceso al sistema. El propio PHP crea el primer usuario
-- al abrir la pantalla de inicio de sesion por primera vez.
CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(60) NOT NULL,
  usuario VARCHAR(30) NOT NULL,
  clave VARCHAR(255) NOT NULL,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario (usuario)
) ENGINE=InnoDB;

-- Verificacion final: deben aparecer 16 tablas, todas vacias.
SHOW TABLES;
