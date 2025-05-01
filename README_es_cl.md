# Módulo de Asistencia para Moodle

Este plugin permite a los profesores tomar asistencia de los alumnos a las clases programadas en Moodle.

## Descripción

El módulo de asistencia permite a los profesores registrar fácilmente la asistencia de los estudiantes a las clases y cursos en la plataforma Moodle. Los estudiantes pueden visualizar su propio registro de asistencia.

## Características

- Registro de múltiples sesiones por curso
- Diferentes estados de asistencia: Presente, Ausente, Atrasado, Justificado
- Posibilidad de agregar observaciones a cada registro
- Bloqueo de sesiones pasadas para evitar modificaciones no autorizadas
- Registro de auditoría de cambios en los datos de asistencia
- Informes detallados para profesores
- Vista personalizada para estudiantes de su propio registro

## Instalación

1. Descomprimir el archivo en la carpeta `mod/attendance` de su instalación de Moodle
2. Acceder como administrador para completar el proceso de instalación

## Adaptaciones para Chile

Este plugin incluye las siguientes adaptaciones específicas para el sistema educacional chileno:

1. **Terminología adecuada**: Se utiliza "atrasado" en lugar de "tarde" y "justificado" en lugar de "excusado".

2. **Formato de horas**: Se utiliza el formato de 24 horas, habitual en Chile.

3. **Compatibilidad con el sistema educativo**: El registro de asistencia es compatible con los requerimientos de asistencia del sistema educativo chileno, donde se requiere un mínimo de asistencia para aprobar cursos.

## Uso básico

### Para profesores:

1. Agregar la actividad "Asistencia" a un curso
2. Crear sesiones con fechas y duraciones específicas
3. Tomar asistencia para cada sesión
4. Consultar informes de asistencia

### Para estudiantes:

1. Acceder a la actividad de asistencia en el curso
2. Visualizar su registro personal de asistencia

## Permisos

El plugin define varios permisos que pueden ser asignados a diferentes roles:

- `mod/attendance:addinstance`: Agregar una nueva actividad de asistencia
- `mod/attendance:managesessions`: Administrar sesiones de asistencia
- `mod/attendance:takeattendances`: Tomar asistencia
- `mod/attendance:view`: Ver información de asistencia
- `mod/attendance:viewown`: Ver registros de asistencia propios
- `mod/attendance:viewreports`: Ver informes de asistencia
- `mod/attendance:changeattendances`: Modificar registros de asistencia existentes

## Seguridad

Este plugin implementa medidas de seguridad como:

- Validación estricta de entrada de datos
- Sanitización de salida para prevenir XSS
- Sistema de bloqueo para prevenir modificaciones no autorizadas
- Registro de auditoría para cambios en los datos

## Soporte

Para cualquier consulta o problema relacionado con este plugin, puede contactar al desarrollador o consultar la documentación de Moodle para desarrollo de plugins.

---

Desarrollado por [Tu Nombre] para el sistema Moodle. 