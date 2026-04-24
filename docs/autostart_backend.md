# Flora AI Auto-Start (One-Time Setup)

This setup makes backend services start automatically at Windows login, so you don't need to run batch files every time.

## What it auto-starts
- Apache (XAMPP)
- MySQL (XAMPP)
- Flora AI service (`ai_service/app.py` on port 5001)

## One-time setup
1. Right-click and run as Administrator:
   - `install_autostart_task.bat`
2. Log out and log in again (or restart PC).

## Manual start (any time)
- `start_all_services.bat`

## Remove auto-start
- Run `remove_autostart_task.bat`

## For phone on mobile data / outside Wi-Fi
Local LAN URLs won't work outside your home/office network.
Use a **public backend URL** once and keep it stable.

Set this in Flutter config:
- `plant_app_flutter/lib/config.dart`
- `Config.publicBaseUrl = "https://your-domain.com/plant_app/api";`

Then rebuild app once.

## Permanent public endpoint (recommended)
Use Cloudflare Tunnel with a fixed domain/subdomain.

### One-time setup
1. Run (as Admin):
   - `setup_cloudflare_tunnel.bat`
2. Sync URL into Flutter config:
   - `sync_public_url_to_flutter.bat`
3. Rebuild app once.

### Daily use
- Just use auto-start:
  - `start_all_services.bat`
- If auto-start task is installed, this also runs at Windows logon.

### .env keys used
- `CF_TUNNEL_TOKEN=<your_tunnel_token>`
- `PUBLIC_BASE_URL=https://your-domain-or-subdomain/plant_app/api`

When these are present and `cloudflared` is installed, `start_all_services.bat` starts the tunnel automatically.

## Note about AI accuracy
If Gemini quota is exhausted, AI returns fallback results. This is independent of startup automation.
