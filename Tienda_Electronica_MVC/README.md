# IMPORTANTE: LEE PRIMERO `LEEME_ENTREGA.md` PARA INSTALAR ESTA EDICIÓN

# Movill Store · Sistema POS (PHP + MySQL, patrón MVC)

## Instalación en XAMPP
1. Copia esta carpeta a `C:\xampp\htdocs\Tienda_Electronica_MVC`.
2. Inicia **Apache** y **MySQL** en el panel de XAMPP.
3. Base de datos (elige UNA opción):
   - **Ya tienes `tienda_electronica` creada desde Workbench:** no hagas nada más (opcional: ejecuta `database/00_ajustes_si_ya_tienes_la_bd.sql`).
   - **Base nueva:** en phpMyAdmin → Importar → `database/01_estructura.sql`, y si quieres datos de prueba, después `database/02_datos_demo.sql`.
4. Revisa usuario/contraseña en `config/conexion.php` (XAMPP trae `root` y contraseña vacía).
5. Abre `http://localhost/Tienda_Electronica_MVC/`
6. Inicio de sesión: la primera vez el sistema crea la tabla `usuarios` y un usuario inicial:
   **usuario `admin` · contraseña `admin123`**. Cámbiala desde el avatar (arriba a la derecha) → *Cambiar contraseña*.
   - La zona horaria está fijada en `America/Bogota` (en `index.php` y `config/conexion.php`).
   - El botón de las tres rayas contrae el menú para dejar solo los íconos.

## Módulos
| Módulo | Qué hace |
|---|---|
| Inicio | Dashboard: KPIs, ventas vs compras (12 meses), clientes y productos top, forma de pago, proveedores, stock bajo |
| Tienda | Catálogo estilo tienda en línea: tarjetas con foto, buscador, filtros por marca/categoría/precio, orden, paginación, detalle del producto y carrito |
| Productos | CRUD con marca, tipo, colores y materiales; foto y descripción; botón "Vender" |
| Ventas | CRUD con varias líneas; descuenta stock y valida existencias |
| Compras | CRUD con varias líneas; suma stock |
| Clientes / Proveedores | CRUD |
| Catálogos | CRUD de ciudades, marcas, tipos, colores, materiales y formas de pago |

Al editar o eliminar una venta/compra el stock se recalcula automáticamente (todo en transacción).
No se puede eliminar un registro que esté siendo usado por otros (ej. un cliente con ventas).

## Estructura
```
index.php            controlador frontal (index.php?c=ventas&a=editar&id=3)
core/                helpers y controlador base
config/conexion.php  conexión PDO
controllers/         un controlador por módulo (+ CrudController y DocumentoController reutilizables)
models/              acceso a datos (Crud, Producto, Documento → Venta/Compra, Dashboard)
views/               plantillas (layout, crud, productos, documentos, dashboard)
assets/              css y js
database/            scripts SQL
```
Los gráficos usan Chart.js desde CDN: necesitan internet.

## Tienda e imágenes
- Las fotos que subas desde **Productos → Agregar/Editar** se guardan en `uploads/productos/` (JPG, PNG, WEBP o GIF, máximo 4 MB). La carpeta debe poder escribirse (en XAMPP ya funciona).
- Los productos sin foto muestran una imagen genérica. Los 20 productos de demostración usan las ilustraciones de `assets/img/demo/`.
- Si ya tenías datos de demostración cargados, ejecuta `database/03_imagenes_demo.sql` para asignarles imagen y descripción.
- El botón **Agregar al carrito** guarda los productos en el navegador y el ícono abre ahora **Mi carrito**; desde ahí se continúa hacia la venta interna.
- Si la subida falla con imágenes grandes, sube `upload_max_filesize` y `post_max_size` en `php.ini` (4M o más).
