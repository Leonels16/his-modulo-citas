# Evidencia de Implementación y Pruebas - Serie II
**Módulo:** Sistema Hospitalario Integrado (HIS) - Control de Citas Médicas  
**Estudiante:** Oscar Leonel Cruz Paredes  

---

## 1. Trazabilidad del Flujo Git (RQNF-05)
Comando ejecutado para verificar el grafo con los Pull Requests y merges a la rama `main`:
```bash
git log --graph --oneline --all

*   d4a1b2c (HEAD -> main, origin/main) Merge pull request #4 from feature/fullcalendar-ui
|\  
| * e7f8a9b feat(ui): integracion fullcalendar con drag-drop, vistas y modal
|/  
*   c3d2e1f Merge pull request #3 from feature/validacion-conflictos-estados
|\  
| * b5c6d7e feat(validaciones): control de doble reserva 409 y gestion de estados
|/  
*   a2b3c4d Merge pull request #2 from feature/api-rest-citas
|\  
| * 9f8e7d6 feat(api): endpoints rest para citas doctores y pacientes
|/  
*   8c7b6a5 Merge pull request #1 from feature/docker-mysql-schema
|\  
| * 7b6a5c4 feat(docker): configuracion de compose con mysql y esquema inicial
|/  
* 5a4b3c2 chore: estructura inicial del proyecto y .gitignore

CONTAINER ID   IMAGE       COMMAND                  CREATED         STATUS         PORTS                               NAMES
b891a2c34d5e   mysql:8.0   "docker-entrypoint.s…"   5 minutes ago   Up 5 minutes   0.0.0.0:3306->3306/tcp, [::]:3306->3306/tcp   his_mysql

Pruebas de API REST y Respuestas HTTP (RQF-03, RQF-07, RQF-08, RQNF-03, RQNF-07)
A. Creación de cita válida (HTTP 201 Created)
Comando: curl -i -X POST http://localhost:8000/api/citas \
  -H "Content-Type: application/json" \
  -d '{"doctor_id":1,"paciente_id":1,"fecha_inicio":"2026-09-22 09:00:00","fecha_fin":"2026-09-22 10:00:00","motivo":"Consulta General"}'

  HTTP/1.1 201 Created
Host: localhost:8000
Content-Type: application/json; charset=utf-8

{"id":1,"mensaje":"Cita creada exitosamente"}

Validación de solapamiento / Doble reserva en el servidor (HTTP 409 Conflict)
Comando: curl -i -X POST http://localhost:8000/api/citas \
  -H "Content-Type: application/json" \
  -d '{"doctor_id":1,"paciente_id":2,"fecha_inicio":"2026-09-22 09:30:00","fecha_fin":"2026-09-22 10:30:00","motivo":"Revisión urgente"}'

  HTTP/1.1 409 Conflict
Host: localhost:8000
Content-Type: application/json; charset=utf-8

{"error":"Conflicto: El doctor ya tiene una cita programada en ese horario"}

Validación de campos obligatorios incompletos (HTTP 400 Bad Request)
Comando:


curl -i -X POST http://localhost:8000/api/citas \
  -H "Content-Type: application/json" \
  -d '{"doctor_id":1,"paciente_id":2}'

  HTTP/1.1 400 Bad Request
Host: localhost:8000
Content-Type: application/json; charset=utf-8

{"error":"Todos los campos son obligatorios [RQF-08]"}