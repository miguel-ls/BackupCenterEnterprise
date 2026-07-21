# Autenticación de instalación

Este flujo permite autenticar el instalador o el worker contra el servidor usando:

- connectionId
- installToken

## Endpoint

POST /api/install-auth.php

## Ejemplo de cuerpo

```json
{
  "connectionId": 1,
  "installToken": "xxxxxxxxxxxxxxxx"
}
```

## Respuesta esperada

```json
{
  "success": true,
  "message": "Autenticación correcta"
}
```
