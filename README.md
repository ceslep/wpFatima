# wpFatima

WhatsApp Messenger app con Twilio + Svelte 5 + PHP

## Stack

- **Frontend**: Svelte 5 (runes) + TypeScript + Tailwind CSS 4
- **Backend**: PHP 8.1+ + Twilio SDK
- **DB**: MySQL
- **API**: REST (JSON)

## Estructura

```
wpFatima/
├── src/                    # Frontend Svelte 5
├── server/                 # Backend PHP
│   ├── app/                # Clases PHP
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Config.php
│   │   └── Database.php
│   ├── public/             # Entry point PHP
│   │   ├── index.php
│   │   └── .htaccess
│   ├── composer.json
│   ├── .env.example
│   └── schema.sql
├── package.json
└── vite.config.ts
```

## Setup local

### Backend

```bash
cd server
cp .env.example .env
composer install
# Importar schema.sql en MySQL
```

### Frontend

```bash
npm install
npm run dev
```

## Deploy

1. `npm run build`
2. Subir contenido de `dist/` a `wpf/` en el hosting
3. Subir `server/` a `wpf/` en el hosting
4. Renombrar `.env.example` a `.env` y configurar credenciales
5. Importar `schema.sql` en phpMyAdmin
6. Configurar webhook en Twilio: `https://domain/wpf/public/webhook/whatsapp`

## API Endpoints

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/messages` | Listar conversaciones |
| GET | `/api/messages/{id}` | Mensajes de una conversacion |
| POST | `/api/send` | Enviar mensaje WhatsApp |
| POST | `/webhook/whatsapp` | Webhook Twilio (inbound) |
