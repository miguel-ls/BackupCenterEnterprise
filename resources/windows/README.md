# Instalador mínimo de Windows

Este directorio contiene un script PowerShell inicial para preparar la instalación de BackupCenter en Windows.

El flujo actual prepara la carpeta de instalación, crea un archivo config.json y deja un registro de instalación. Además intenta registrar un servicio Windows básico llamado BackupCenterWorker.

También genera un launcher simple para iniciar el worker y un archivo worker.log para registrar el arranque.

Además, el paquete incluye un launcher de prueba de Windows que puede usarse para validar el flujo de inicio del worker.

También se incluye un script para registrar un servicio Windows básico llamado BackupCenterWorker, y otro para eliminarlo si es necesario.

Además, el paquete incluye un comprobador de configuración que valida que el archivo config.json exista y contenga los datos mínimos de instalación.

También incluye una guía básica de autenticación para validar el token de instalación contra el endpoint /api/install-auth.php.

Y una guía breve de notificaciones para confirmar que el sistema registra eventos cuando se genera un paquete de instalación.

Si el paquete descargado trae un archivo config.json, el instalador lo copiará a la carpeta de destino para que la configuración quede lista de forma automática.

## Uso

1. Ejecutar el script desde PowerShell:

```powershell
./install.ps1
```

2. El script crea la carpeta:

```text
C:\Program Files\BackupCenter
```

3. Genera un archivo base de configuración:

```text
config.json
```

Este flujo es un primer paso para BC-048 y podrá evolucionar hacia un instalador real con servicio Windows y actualización automática.
