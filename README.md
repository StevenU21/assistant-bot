# Proyecto Laravel con Inertia.js y Vue.js para Consumir la API de OpenAI

## ✨ Descripción
Este proyecto es una aplicación web desarrollada con Laravel 11 y Vue.js utilizando Inertia.js como puente entre el frontend y el backend. Su objetivo principal es consumir los servicios de la API de OpenAI, permitiendo a los usuarios acceder a funcionalidades avanzadas como:

- **Transcripción de audio a texto** (con reproductor de audio integrado).
- **Traducción de textos**.
- **Conversión de texto a voz**.
- **Generación de imágenes basadas en texto**.
- **Chatbot interactivo** impulsado por la API de OpenAI.

### Características clave:
- **Uso de Jobs**: Para manejar tareas de larga duración y mantener el rendimiento de la aplicación.
- **Broadcasting con Laravel Reverb**: Para actualizaciones en tiempo real.
- **Integración de DomPDF**: Exporta las transcripciones en formato PDF.
- **Frontend moderno con Inertia.js y Vue.js**: Experiencia fluida y reactiva.
- **Reproductor de audio**: Permite escuchar las transcripciones generadas.

## 🔧 Requisitos del sistema
- **PHP**: Versión 8.2 o superior.
- **Composer**: Gestor de dependencias PHP.
- **Node.js**: Versión 18 o superior.
- **NPM**: Versión 8 o superior.
- **MySQL o PostgreSQL**: Para la base de datos.
- **Cuenta en OpenAI**: Debes generar tu propia API Key para usar los servicios.

## 📝 Instalación
Sigue estos pasos para configurar y ejecutar el proyecto:

### 1. Clonar el repositorio
```bash
 git clone https://github.com/StevenU21/assistant-bot.git
```
```bash
 cd assistan-bot && code .
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de Node.js
```bash
npm install
```

### 4. Configurar el archivo `.env`
Copia el archivo de ejemplo y personalízalo:
```bash
cp .env.example .env
```
- Configura los detalles de conexión a la base de datos.
- Agrega tu clave API de OpenAI:
  ```env
  OPENAI_API_KEY=tu_api_key_aqui
  ```

### 5. Generar la clave de aplicación y migrar la base de datos
```bash
php artisan key:generate
php artisan migrate
```

### 6. Construir los assets del frontend
```bash
npm run dev
```

### 7. Iniciar el servidor de desarrollo y servicios requeridos
Ejecuta los siguientes comandos en terminales separadas:

1. Servidor de Laravel:
   ```bash
   php artisan serve
   ```

2. Servidor de Vite:
   ```bash
   npm run dev
   ```

3. Reverb para broadcasting:
   ```bash
   php artisan reverb:start
   ```

4. Trabajos en segundo plano:
   ```bash
   php artisan queue:work
   ```

### 8. Acceso a la aplicación
Abre tu navegador y accede a la aplicación en:
```
http://localhost:8000
```

## 🎨 Funcionalidades

### Transcripción de Audio
Sube archivos de audio y genera transcripciones precisas con un reproductor para escuchar el resultado.

### Traducción
Traduce textos a múltiples idiomas utilizando los modelos avanzados de OpenAI.

### Texto a Voz
Convierte texto en audio con voces naturales y personalizables.

### Generación de Imágenes
Crea imágenes únicas a partir de descripciones detalladas.

### Chatbot
Interactúa con un chatbot basado en IA para responder preguntas y realizar tareas personalizadas.

## ⚙️ Tecnologías utilizadas
- **Backend**: Laravel 11
- **Frontend**: Vue.js con Inertia.js
- **Broadcasting**: Laravel Reverb y Echo
- **PDF**: DomPDF
- **Cola de trabajos**: Laravel Queue

## 📊 Recursos adicionales
- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Vue.js](https://vuejs.org/)
- [API de OpenAI](https://platform.openai.com/docs)

---
