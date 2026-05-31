# Propuesta de vista de detalle para Mascotas

## Estado actual

En la pantalla de mascotas, la acción **Ver** todavía no está implementada como una vista real. Hoy la función `verMascota(id)` solo muestra una alerta temporal en [public/js/recepcion/mascotas.js](public/js/recepcion/mascotas.js#L256).

La vista actual de alta/edición ya usa un modal centrado sobre un overlay oscuro en [resources/views/dash/recepcion/mascotas.blade.php](resources/views/dash/recepcion/mascotas.blade.php#L103), así que la propuesta más consistente es reutilizar ese mismo estándar visual.

## Propuesta funcional

La opción **Ver** debe abrir un **modal de detalle de mascota** en modo solo lectura. Ese modal debe mostrar la información principal de forma jerárquica, sin campos editables, para que el usuario entienda rápido el estado del paciente.

Si en una etapa posterior se necesita más profundidad, la misma vista puede evolucionar a una página de detalle completa, pero el primer paso recomendado es el modal porque mantiene el flujo actual de trabajo.

## Estándar visual

Cuando se abra cualquier formulario o detalle, la pantalla de fondo debe quedar desenfocada y desactivada visualmente. La regla estándar propuesta es esta:

1. Overlay semitransparente para bloquear la interacción con el contenido de fondo.
2. `backdrop-filter: blur(...)` o una capa visual equivalente para reforzar el enfoque en el modal.
3. Cierre con botón, tecla `Escape` y clic fuera del modal.
4. Misma estructura base para formularios y detalles, cambiando solo el contenido interno.

Así se evita que cada pantalla tenga un comportamiento distinto cuando aparece una ventana emergente.

## Composición sugerida del modal de detalle

### Encabezado

- Nombre de la mascota.
- ID visible.
- Estado con etiqueta de color.
- Botón de cerrar.

### Bloque principal

- Foto o avatar de la mascota.
- Especie, raza, sexo y edad.
- Peso, estado y última visita.
- Propietario asociado con acceso directo a su ficha.

### Secciones complementarias

- Resumen clínico.
- Vacunas o antecedentes relevantes.
- Últimas citas.
- Observaciones del personal.

### Acciones

- Ver expediente.
- Ver historial médico.
- Editar mascota.
- Cerrar.

## Wireframe de referencia

```text
┌──────────────────────────────────────────────────────────┐
│ Mascota: Luna                               [Cerrar]    │
├──────────────────────────────────────────────────────────┤
│  Avatar / foto        │  Datos básicos                  │
│                       │  ID                             │
│                       │  Especie                        │
│                       │  Raza                           │
│                       │  Sexo                           │
│                       │  Edad                           │
│                       │  Estado                         │
├──────────────────────────────────────────────────────────┤
│ Propietario | Última visita | Peso | Observaciones      │
├──────────────────────────────────────────────────────────┤
│ Historial breve / vacunas / citas recientes             │
├──────────────────────────────────────────────────────────┤
│ [Ver expediente] [Editar] [Cerrar]                      │
└──────────────────────────────────────────────────────────┘
```

## Flujo recomendado

1. El usuario pulsa **Ver** en la tabla.
2. Se abre el modal con overlay y blur del fondo.
3. Se carga la mascota seleccionada por ID.
4. El contenido se muestra en formato de lectura, no editable.
5. Si el usuario necesita modificar algo, usa **Editar** desde el mismo modal o vuelve a la tabla.

## Ventajas de esta propuesta

- Mantiene el patrón visual que ya existe en el módulo.
- Reduce ruido porque el usuario no abandona la lista para ver un detalle puntual.
- Permite evolucionar a página completa sin romper la lógica actual.
- Unifica el comportamiento de apertura de formularios con el mismo efecto de blur.

## Recomendación final

Para este proyecto, la mejor primera versión es un **modal de detalle con blur**. Es la opción más coherente con la vista actual, más rápida de implementar y más fácil de estandarizar para futuras pantallas.