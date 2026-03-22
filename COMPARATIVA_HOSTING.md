# Comparativa de Hosting para RentCar

Documento de referencia rapida para evaluar opciones de hosting para este proyecto.

Proyecto considerado:

- Backend Laravel
- Frontend Vue + Vite
- Autenticacion, roles, panel administrativo y carga de archivos
- Base de datos relacional

Notas:

- Los precios son aproximados a marzo de 2026.
- Pueden variar por region, impuestos, consumo real y extras.
- No se incluyen dominio ni correo transaccional, salvo aclaracion.

## Resumen Ejecutivo

| Escenario | Opcion | Costo aprox. mensual | Ventaja principal | Desventaja principal |
| --- | --- | ---: | --- | --- |
| Muy economico | Hetzner VPS solo | EUR 4.49 a EUR 11.99 | Mejor costo/rendimiento | Mas trabajo operativo |
| Equilibrado | Hetzner + Forge | USD 17 a USD 32 aprox. | Buen balance entre costo y comodidad | Pagas capa extra de gestion |
| Equilibrado mainstream | DigitalOcean + Forge | USD 24 a USD 43 aprox. | Ecosistema muy conocido | Mas caro que Hetzner |
| Cero DevOps | Railway + Vercel | USD 5 a USD 25 al inicio | Despliegue muy rapido | Costo variable por uso |
| Cero DevOps con mas estructura | Render + Vercel | USD 13 a USD 35 al inicio | Muy simple de operar | Puede crecer de costo al sumar servicios |

## Comparativa por Proveedor

| Proveedor | Tipo | Entrada aprox. | Rango util para este proyecto | Comentario |
| --- | --- | ---: | ---: | --- |
| Hetzner Cloud | VPS | EUR 2.99 | EUR 4.49 a EUR 11.99 | Excelente precio, ideal si queres VPS serio y barato |
| DigitalOcean Droplets | VPS | USD 4 | USD 12 a USD 24 | Muy popular y simple, algo mas caro |
| Laravel Forge | Gestion Laravel | USD 12 | USD 12 a USD 19 | Simplifica deploy, SSL, workers y sitios |
| Render | PaaS | USD 0 | USD 13 a USD 35 | Facil de operar, buena opcion sin DevOps |
| Railway | PaaS | USD 0 a USD 5 minimo | USD 5 a USD 25 al inicio | Pago por uso, muy comodo para arrancar |
| Vercel | Frontend / CDN | USD 0 | USD 0 a USD 20 | Excelente para frontend Vue estatico |

## Escenarios Recomendados

### 1. Opcion Economica

Pensada para bajar costo al maximo sin salir de una base tecnica solida.

| Componente | Opcion | Costo aprox. |
| --- | --- | ---: |
| Servidor | Hetzner Cloud regular | EUR 4.49 a EUR 11.99 |
| Backend Laravel | En el mismo VPS | Incluido |
| Frontend Vue | Build servido por Nginx en el mismo VPS | Incluido |
| Base de datos | MySQL o MariaDB en el mismo VPS | Incluido |
| SSL | Let's Encrypt | USD 0 |
| Total aprox. | | EUR 4.49 a EUR 11.99 |

Ventajas:

- Costo muy bajo
- Todo queda en un unico servidor
- Buena base para MVP o sistema interno

Desventajas:

- Mas responsabilidad operativa
- Backups, deploy y monitoreo dependen de tu configuracion

### 2. Opcion Equilibrada

Pensada para produccion real con menor carga operativa.

| Componente | Opcion | Costo aprox. |
| --- | --- | ---: |
| Servidor | Hetzner Cloud o DigitalOcean | USD 5 a USD 24 |
| Gestion Laravel | Forge Hobby o Growth | USD 12 a USD 19 |
| Backend Laravel | En el VPS via Forge | Incluido |
| Frontend Vue | Build servido por Nginx o sitio separado | Incluido |
| Base de datos | En el mismo VPS o administrada aparte | Incluido o extra |
| SSL | Incluido via Forge / Let's Encrypt | USD 0 |
| Total aprox. | | USD 17 a USD 43 |

Ventajas:

- Muy buen equilibrio entre costo y productividad
- Despliegues mas simples
- Menos friccion operativa con Laravel

Desventajas:

- Mas caro que un VPS puro
- Seguis siendo responsable de la infra base

### 3. Opcion Cero DevOps

Pensada para priorizar simplicidad y velocidad de salida.

| Componente | Opcion | Costo aprox. |
| --- | --- | ---: |
| Backend | Railway o Render | USD 5 a USD 25 |
| Frontend | Vercel Hobby o Pro | USD 0 a USD 20 |
| Base de datos | Railway / Render administrada | USD 0 a USD 19+ |
| SSL | Incluido | USD 0 |
| Total aprox. | | USD 13 a USD 50 |

Ventajas:

- Muy rapido de desplegar
- Menos operacion manual
- Bueno para equipos chicos

Desventajas:

- Costo menos predecible a medida que escala
- Menos control fino que un VPS propio

## Costos de Referencia por Stack

| Stack | Costo mensual aprox. | Perfil recomendado |
| --- | ---: | --- |
| Hetzner solo | EUR 4.49 a EUR 11.99 | MVP, sistema interno, bajo presupuesto |
| Hetzner + Forge | USD 17 a USD 32 aprox. | Produccion chica a mediana |
| DigitalOcean + Forge | USD 24 a USD 43 aprox. | Produccion con proveedor mainstream |
| Railway + Vercel | USD 5 a USD 25 al inicio | Salida rapida, minima operacion |
| Render + Vercel | USD 13 a USD 35 al inicio | App de negocio con menos DevOps |

## Extras a considerar

Estos costos no siempre aparecen en la cuenta inicial, pero hay que contemplarlos.

| Extra | Rango aproximado |
| --- | ---: |
| Dominio | USD 10 a USD 15 por ano |
| Correo transaccional | USD 0 a USD 15 por mes |
| Backups externos | USD 0 a USD 10 por mes |
| Object storage tipo S3 | USD 0 a USD 10+ por mes |
| Monitoreo extra | USD 0 a USD 20+ por mes |

## Recomendacion para este proyecto

### Recomendacion principal

**Hetzner + Forge**

Por que:

- Costo razonable
- Laravel queda comodo de operar
- Sirve bien para backend, frontend compilado, SSL y base de datos
- Evita sobrecosto temprano sin complicar demasiado la operacion

### Alternativa si queres algo mas conocido

**DigitalOcean + Forge**

Por que:

- Muy buen ecosistema
- Documentacion abundante
- Suele ser facil de delegar o mantener

### Alternativa si queres tocar lo minimo posible de infraestructura

**Railway + Vercel**

Por que:

- Muy rapido de poner online
- Backend y frontend quedan desacoplados
- Bueno para iterar rapido

## Decision Rapida

| Si tu prioridad es... | Opcion recomendada |
| --- | --- |
| Ahorrar al maximo | Hetzner VPS |
| Equilibrio entre costo y prolijidad | Hetzner + Forge |
| Proveedor mas mainstream | DigitalOcean + Forge |
| Casi cero DevOps | Railway + Vercel |
| PaaS mas estructurado | Render + Vercel |
