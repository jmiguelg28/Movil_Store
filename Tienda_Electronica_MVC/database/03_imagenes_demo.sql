-- Imágenes y descripciones de los 20 productos de demostración.
-- Las imágenes están en assets/img/demo/. Requiere las columnas imagen y descripcion
-- (el sistema las crea solo al abrir Productos o Tienda; también están en 00 y 01).
USE tienda_electronica;

UPDATE producto SET imagen = 'assets/img/demo/p1.png', descripcion = 'Samsung Galaxy S24 de 128 GB con pantalla AMOLED de 6.2 pulgadas, procesador de última generación y cámara triple de 50 MP. Incluye cable y cargador.' WHERE cod_producto = 1;
UPDATE producto SET imagen = 'assets/img/demo/p2.png', descripcion = 'iPhone 15 de 128 GB con chip A16 Bionic, cámara principal de 48 MP y puerto USB-C. Pantalla Super Retina XDR de 6.1 pulgadas.' WHERE cod_producto = 2;
UPDATE producto SET imagen = 'assets/img/demo/p3.png', descripcion = 'Xiaomi Redmi Note 13 con pantalla AMOLED de 120 Hz, cámara de 108 MP y batería de larga duración con carga rápida.' WHERE cod_producto = 3;
UPDATE producto SET imagen = 'assets/img/demo/p4.png', descripcion = 'Motorola Moto G54 5G con pantalla de 6.5 pulgadas, 8 GB de RAM y batería de 6000 mAh para todo el día.' WHERE cod_producto = 4;
UPDATE producto SET imagen = 'assets/img/demo/p5.png', descripcion = 'MacBook Air con chip M2, pantalla Liquid Retina de 13.6 pulgadas, 8 GB de RAM y hasta 18 horas de batería. Diseño delgado y silencioso.' WHERE cod_producto = 5;
UPDATE producto SET imagen = 'assets/img/demo/p6.png', descripcion = 'Portátil HP Pavilion de 15.6 pulgadas, procesador Intel Core i5, 16 GB de RAM y SSD de 512 GB. Ideal para estudio y trabajo.' WHERE cod_producto = 6;
UPDATE producto SET imagen = 'assets/img/demo/p7.png', descripcion = 'Lenovo IdeaPad 3 de 15.6 pulgadas con procesador Ryzen 5, 8 GB de RAM y SSD de 512 GB. Rendimiento fluido para el día a día.' WHERE cod_producto = 7;
UPDATE producto SET imagen = 'assets/img/demo/p8.png', descripcion = 'Audífonos inalámbricos con cancelación de ruido líder en su categoría, hasta 30 horas de batería y micrófonos para llamadas nítidas.' WHERE cod_producto = 8;
UPDATE producto SET imagen = 'assets/img/demo/p9.png', descripcion = 'AirPods Pro de segunda generación con cancelación activa de ruido, audio espacial y estuche de carga MagSafe.' WHERE cod_producto = 9;
UPDATE producto SET imagen = 'assets/img/demo/p10.png', descripcion = 'Audífonos Bluetooth JBL Tune 520BT con sonido JBL Pure Bass y hasta 57 horas de batería. Plegables y livianos.' WHERE cod_producto = 10;
UPDATE producto SET imagen = 'assets/img/demo/p11.png', descripcion = 'Tablet Samsung Galaxy Tab S9 con pantalla Dynamic AMOLED de 11 pulgadas, resistencia al agua y S Pen incluido.' WHERE cod_producto = 11;
UPDATE producto SET imagen = 'assets/img/demo/p12.png', descripcion = 'Xiaomi Pad 6 con pantalla de 11 pulgadas a 144 Hz, cuatro parlantes y carga rápida de 33 W.' WHERE cod_producto = 12;
UPDATE producto SET imagen = 'assets/img/demo/p13.png', descripcion = 'Mouse inalámbrico ergonómico con sensor de alta precisión, rueda silenciosa y batería de hasta 70 días. Conexión Bluetooth y USB.' WHERE cod_producto = 13;
UPDATE producto SET imagen = 'assets/img/demo/p14.png', descripcion = 'Teclado Bluetooth compacto que se conecta a tres dispositivos y cambia entre ellos con una tecla. Teclas silenciosas y cómodas.' WHERE cod_producto = 14;
UPDATE producto SET imagen = 'assets/img/demo/p15.png', descripcion = 'Cargador de pared de carga rápida de 65 W con puerto USB-C. Compatible con celulares, tablets y portátiles.' WHERE cod_producto = 15;
UPDATE producto SET imagen = 'assets/img/demo/p16.png', descripcion = 'Parlante portátil resistente al agua y al polvo con sonido potente, graves profundos y hasta 12 horas de reproducción.' WHERE cod_producto = 16;
UPDATE producto SET imagen = 'assets/img/demo/p17.png', descripcion = 'Parlante Bluetooth compacto Sony con diseño resistente al agua y 16 horas de batería. Perfecto para llevar a todas partes.' WHERE cod_producto = 17;
UPDATE producto SET imagen = 'assets/img/demo/p18.png', descripcion = 'Reloj inteligente Galaxy Watch 6 con monitoreo de sueño, frecuencia cardíaca y pantalla AMOLED siempre activa.' WHERE cod_producto = 18;
UPDATE producto SET imagen = 'assets/img/demo/p19.png', descripcion = 'Apple Watch SE con detección de caídas, seguimiento de actividad y notificaciones. Resistente al agua.' WHERE cod_producto = 19;
UPDATE producto SET imagen = 'assets/img/demo/p20.png', descripcion = 'Smart band Xiaomi Mi Band 8 con pantalla AMOLED de 1.62 pulgadas, más de 150 modos deportivos y hasta 16 días de batería.' WHERE cod_producto = 20;
