# Sistema-de-control-de-tareas-escolares
# Product Backlog — Sistema de Organización tipo Notion

**Proyecto:** Aplicación web de organización de notas  
**Responsable frontend:** Peralta Trujillo Oliver  
**Responsable backend:** López Xochiquiquixqui Uriel/Sánchez Marín Maria del Rosario
**Fecha de inicio:** 11/05/2026 
**Fecha de entrega:** Entre 01/06/2026 y 03/06/2026
**Tecnologías:** HTML5 / CSS3 / JavaScript — VS Code

-----

## Leyenda de Prioridad

|Prioridad|Urgencia      |Sprint objetivo|
|---------|--------------|---------------|
|🔴 Alta   |Imprescindible|Sprint 1–2     |
|🟠 Media  |Importante    |Sprint 3       |
|🟢 Baja   |Deseable      |Sprint 4–5     |

-----

## Historias de Usuario

|ID  |Título                                 |Descripción                                                                                                                    |Criterios de Aceptación                                                                                                                                              |Prioridad|Sprint  |Estimación|Responsable            |Estado       |
|----|---------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------|---------|--------|----------|-----------------------|-------------|
|HU01|Estructura base del proyecto           |Creación de archivos base: `index.html`, `style.css`, `app.js` con estructura HTML5 válida                                     |Archivos creados y enlazados correctamente. Proyecto abre en navegador sin errores.                                                                                  |🔴 Alta   |Sprint 1|2 h       |Peralta Trujillo Oliver|✅ Completado |
|HU02|Tablero principal con creación de notas|Tablero vacío con botón `+` que al hacer clic muestra formulario de título; la nota guardada aparece como tarjeta en el tablero|Tablero visible al abrir página. Botón `+` presente. Formulario aparece al clic. Nota se muestra como tarjeta. Tarjetas se acumulan. Sin persistencia (localStorage).|🔴 Alta   |Sprint 1|4 h       |Peralta Trujillo Oliver|🔄 En progreso|
|HU03|Edición y eliminación de notas         |El usuario puede editar el título/contenido de una tarjeta existente y eliminarla del tablero                                  |Botón editar abre formulario con datos actuales. Cambios se reflejan en la tarjeta. Botón eliminar remueve la tarjeta del tablero.                                   |🔴 Alta   |Sprint 2|3 h       |Por asignar            |⏳ Pendiente  |
|HU04|Persistencia con localStorage          |Las notas se guardan en localStorage para que no se pierdan al recargar la página                                              |Al recargar, las notas siguen apareciendo. Al eliminar una nota, desaparece también del almacenamiento.                                                              |🔴 Alta   |Sprint 2|2 h       |Por asignar            |⏳ Pendiente  |
|HU05|Contenido enriquecido en las notas     |Cada nota puede tener, además del título, un cuerpo de texto con formato básico (negrita, cursiva, listas) al estilo Notion    |Editor muestra área de contenido al abrir una nota. Soporte de al menos negrita, cursiva y lista con viñetas.                                                        |🟠 Media  |Sprint 3|5 h       |Por asignar            |⏳ Pendiente  |
|HU06|Organización por columnas / categorías |El usuario puede agrupar notas en columnas o categorías arrastrando tarjetas (estilo kanban)                                   |Columnas visibles en el tablero. Drag & drop funcional entre columnas. El estado se persiste.                                                                        |🟠 Media  |Sprint 3|6 h       |Por asignar            |⏳ Pendiente  |
|HU07|Búsqueda de notas                      |Barra de búsqueda que filtra las tarjetas en tiempo real por título o contenido                                                |Campo de búsqueda visible. Tarjetas se filtran mientras se escribe. Sin resultados muestra mensaje amigable.                                                         |🟠 Media  |Sprint 4|3 h       |Por asignar            |⏳ Pendiente  |
|HU08|Modo oscuro                            |Toggle para cambiar entre tema claro y oscuro, guardando la preferencia del usuario                                            |Botón de toggle visible. Tema cambia en toda la interfaz. Preferencia persiste en localStorage.                                                                      |🟢 Baja   |Sprint 4|2 h       |Por asignar            |⏳ Pendiente  |
|HU09|Integración con backend / base de datos|Las notas se sincronizan con un servidor (API REST) para persistencia real multi-dispositivo                                   |CRUD completo vía API. Manejo de errores de red. Indicador de carga en operaciones.                                                                                  |🟢 Baja   |Sprint 5|8 h       |Por asignar            |⏳ Pendiente  |

-----


## Notas del equipo

- **HU01** y **HU02** corresponden al trabajo registrado en el documento `PTO_HU_11-12`.
- **HU03** en adelante son historias proyectadas para completar el producto mínimo viable.
- La persistencia real con backend (**HU09**) está deliberadamente pospuesta, conforme a la decisión tomada en HU02.
- Las estimaciones son orientativas y deben ajustarse en la planificación de cada sprint.

## INTENGRANTES:
- Caballero Ortiz Nancy Denisse- ducumentadora
- López Xochiquiquixqui Uriel -product owner
- Peralta Trujillo Oliver -Delevelopers
- Sánchez Marín María del Rosario -scrum máster
