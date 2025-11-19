README – Sistema de Adopción de Mascotas “Adopta Meli”
🐶🐱 Sistema Web de Adopción de Mascotas – Adopta Meli

Este proyecto es un sistema web diseñado para facilitar la publicación, visualización y gestión de mascotas disponibles para adopción en refugios o fundaciones.
Permite a instituciones registrar mascotas, subir fotografías y mostrar información relevante para que los usuarios puedan encontrar un compañero ideal.

🔥 Objetivo General

Crear una plataforma web sencilla, amigable y funcional que permita gestionar mascotas en adopción, mostrando su información esencial de manera clara.

📌 Requerimientos Funcionales
RF01 – Registro de Mascotas

El sistema debe permitir registrar mascotas con los siguientes datos:

Nombre

Institución/Refugio

Tipo (Perro, Gato u otro)

Raza

Edad

Tamaño

Estado (Disponible / Adoptado)

Descripción

Fotografía (almacenada en BLOB)

RF02 – Mostrar Mascotas Disponibles

El sistema debe listar las mascotas con estado Disponible junto con:

Nombre

Foto

Tipo

Raza

Edad

Tamaño

Institución a la que pertenece

RF03 – Visualización de Fotografía

El sistema debe recuperar fotografías almacenadas como BLOB y mostrarlas en el navegador.

RF04 – Selección de Institución

Al registrar una mascota, debe ser posible elegir una institución activa desde un menú desplegable.

RF05 – Validación de Datos

El sistema debe validar que:

El nombre esté completo

La institución esté seleccionada

El tipo y tamaño estén seleccionados

La imagen sea válida (si se sube)

RF06 – Almacenamiento en Base de Datos

Toda la información debe ser almacenada en una base de datos MySQL.

📌 Requerimientos No Funcionales
RNF01 – Usabilidad

El sistema debe tener una interfaz clara y fácil de utilizar para usuarios sin experiencia técnica.

RNF02 – Rendimiento

El sistema debe cargar las tarjetas de mascotas sin demoras perceptibles, aun cuando existan muchas en la base de datos.

RNF03 – Mantenibilidad

El código debe estar organizado usando un enfoque tipo MVC:

/vista

/controladores

/modelos

/assets

RNF04 – Seguridad

No deben subirse credenciales a GitHub (conexionBD.php está en .gitignore).

Las imágenes deben ser filtradas antes de almacenarse.

Las consultas deben usar PDO con prepared statements.

RNF05 – Compatibilidad

El sitio debe funcionar correctamente en los navegadores modernos:

Chrome

Edge

Firefox

RNF06 – Adaptabilidad

La presentación debe usar Bootstrap, permitiendo correcta visualización en:

PC

Tablets

Móviles

👥 Historias de Usuario
🧑‍💼 HU01 – Registrar Mascota

Como administrador de una institución
quiero registrar una mascota con sus datos y foto
para que pueda aparecer en la lista de mascotas adoptables.

😺 HU02 – Ver Mascotas Disponibles

Como usuario visitante
quiero ver las mascotas disponibles con su foto y datos
para decidir si quiero adoptar alguna.

📸 HU03 – Mostrar Fotografía

Como usuario
quiero ver una imagen real de cada mascota
para conocer mejor a la mascota antes de solicitar su adopción.

🏠 HU04 – Ver Refugio

Como usuario visitante
quiero saber de qué refugio proviene la mascota
para contactar correctamente a la institución en caso de interés.
