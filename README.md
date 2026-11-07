# Movill Store — Tienda de productos tecnológicos

**Proyecto académico · Aplicación web con PHP, MySQL y arquitectura MVC**

Movill Store es un sistema de administración y venta de productos tecnológicos, como celulares, computadores, tabletas y accesorios. Integra catálogo, gestión de inventario, clientes, proveedores, compras y ventas en una aplicación web que se ejecuta localmente con XAMPP.

> **Alcance:** Es un proyecto académico de ventas e inventario. No incluye una pasarela de pagos en línea ni un sitio público desplegado en internet. El acceso y las operaciones deben verificarse en cada instalación.

## Tecnologías

- **Backend:** PHP y PDO para MySQL/MariaDB.
- **Base de datos:** MySQL o MariaDB (por ejemplo, desde XAMPP).
- **Frontend:** HTML, CSS y JavaScript.
- **Arquitectura:** MVC (modelo–vista–controlador).
- **Entorno local:** Apache mediante XAMPP; phpMyAdmin para administrar MySQL.

## Funcionalidades

- Catálogo visual de productos tecnológicos, con búsqueda y filtros.
- Carrito de compra que permite revisar cantidades y continuar al registro de una **venta interna**.
- Registro y administración de productos, marcas, tipos, colores y materiales.
- Gestión de clientes y proveedores.
- Registro de compras y ventas vinculadas al inventario.
- Panel administrativo con indicadores y reportes.
- Inicio de sesión y cambio de contraseña.

**Nota:** Estas funciones forman parte del código entregado; su funcionamiento completo está sujeto a las pruebas locales de integración con la base de datos.

## Estructura del proyecto

```text
Tienda_Electronica_MVC/
├── index.php            # Punto de entrada y rutas
├── config/              # Conexión a MySQL
├── controllers/         # Controladores
├── models/              # Modelos y consultas
├── views/               # Pantallas
├── core/                # Utilidades y controlador base
├── assets/              # CSS, JavaScript e imágenes
├── uploads/             # Imágenes cargadas de productos
└── database/            # Scripts SQL
```

## Instalación local con XAMPP

1. Instala **XAMPP** e inicia **Apache** y **MySQL**.
2. Copia la carpeta `Tienda_Electronica_MVC` dentro de `C:\xampp\htdocs\`.
3. Abre `http://localhost/phpmyadmin`.
4. Crea una base de datos llamada **`movil_store_nueva`** (con una sola `l` en `movil`).
5. Selecciónala e importa **`Movill_Store_CREAR_16_TABLAS.sql`**, ubicado en la carpeta `database/` de este repositorio. **Verifica antes de ejecutarlo que su instrucción `USE` diga `movil_store_nueva`.** Este SQL crea las 16 tablas sin datos de demostración.
6. Edita `Tienda_Electronica_MVC/config/conexion.php` para que coincida con tu servidor local:

```php
private $host = '127.0.0.1';
private $db = 'movil_store_nueva';
private $usuario = 'root';
private $password = ''; // Solo si tu XAMPP local usa contraseña vacía
```

7. Entra en `http://localhost/Tienda_Electronica_MVC/`.

> **Seguridad:** No subas contraseñas reales, datos de clientes ni respaldos privados al repositorio. Las credenciales indicadas arriba son únicamente un ejemplo de configuración **local**. Para un servidor público, utiliza un usuario MySQL dedicado y variables de entorno.

## Inicio de sesión

El código incluye un mecanismo que, cuando la tabla `usuarios` está vacía, crea una cuenta inicial de desarrollo (`admin` / `admin123`). **Esas credenciales son solo para un entorno local de pruebas.** Cambia la contraseña inmediatamente después del primer acceso. Si ya existe algún usuario, el sistema no vuelve a crear esa cuenta inicial.

El inicio de sesión debe probarse después de instalar la base. No debe darse por validado únicamente porque aparezca la pantalla de login.

## Archivos SQL y precauciones

- `Movill_Store_CREAR_16_TABLAS.sql`: estructura de **16 tablas** para la base nueva `movil_store_nueva`, sin insertar datos de prueba.
- `01_estructura.sql`: estructura original que se refiere a `tienda_electronica`. **No lo ejecutes sobre `movil_store_nueva` sin revisar y adaptar el nombre.**
- `02_datos_demo.sql` y `03_imagenes_demo.sql`: ejemplos opcionales; no se necesitan para empezar con una base vacía.
- `00_ajustes_si_ya_tienes_la_bd.sql` y `04_migracion_segura.sql`: ajustes destinados a otros esquemas o instalaciones; no ejecutes estas migraciones sin comprobar antes sus instrucciones y hacer un respaldo.

## Pruebas recomendadas

- [ ] Iniciar sesión con un usuario válido.
- [ ] Crear marca, tipo de producto y producto.
- [ ] Registrar un cliente y un proveedor.
- [ ] Registrar una compra y verificar el aumento de existencias.
- [ ] Registrar una venta y verificar la reducción de existencias.
- [ ] Probar búsqueda, filtros, carrito y diseño en celular.
- [ ] Cerrar sesión y volver a ingresar.

## Publicación y entrega

Este repositorio contiene el **código fuente**. GitHub permite versionar y presentar el trabajo, pero **GitHub Pages no ejecuta PHP ni MySQL**. Para una demostración pública operativa se necesita un hosting con Apache/PHP y una base de datos MySQL/MariaDB.

**Proyecto académico — Universidad del Tolima.**
