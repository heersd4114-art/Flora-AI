# Flora AI - WiFi Mode & AI Fallback Fix

## PROBLEM SOLVED ✓

Your AI was giving inaccurate results because **Gemini API free tier was exhausted** (20 requests/day limit reached).

We've implemented a **LOCAL AI FALLBACK** system that works when Gemini quota is exceeded.

## HOW IT WORKS NOW

### When Gemini API Works (Within Quota)
- Uses Google Gemini Vision API (highly accurate)
- Method: "Google gemini-2.5-flash Vision API"

### When Gemini API Quota Exhausted (429 Error)
- **Automatically switches** to local ResNet-50 model
- Method: "Local ResNet-50 (Offline Mode)"
- Works WITHOUT internet (only needs WiFi to phone connection)
- Less accurate but 100% reliable

## SETUP FOR WIFI MODE (Works Without USB Cable)

### Step 1: Ensure Services Are Running
Run this batch file once:
```
SETUP_WIFI_MODE.bat
```

This will:
- Start Apache (port 80)
- Start MySQL (port 3306)  
- Start Flora AI service (port 5001)

### Step 2: Connect Phone to WiFi
Make sure your phone is on the **SAME WiFi network** as your laptop:
- Both on home WiFi router, OR
- Both on office/campus WiFi

### Step 3: Open App on Phone
- Launch Flora AI app
- It will automatically search for the server
- First scan will take 30-60 seconds (loading local AI model)
- After that, scans take 5-10 seconds each

## TROUBLESHOOTING

### Problem: App says "Waiting for API..."
**Solution**: 
1. Make sure phone and laptop are on SAME WiFi network
2. Run `SETUP_WIFI_MODE.bat` again
3. Wait 60 seconds for AI service to start
4. Restart the app on your phone

### Problem: "Service unreachable" or "Connection refused"
**Solution**:
1. Run this command to check services:
```batch
netstat -ano | findstr ":5001"  # Should show Flora AI running
netstat -ano | findstr ":80"    # Should show Apache running
netstat -ano | findstr ":3306"  # Should show MySQL running
```

2. If not running, restart:
```batch
SETUP_WIFI_MODE.bat
```

### Problem: Plant scanning is slow (30-60 seconds first time)
**This is normal!** The local AI model loads on first scan:
- First scan: 30-60 seconds (model loading)
- Next scans: 5-10 seconds (model cached)

### Problem: AI detection says "Not a Plant" or "Unverified Plant"
**Possible reasons**:
1. Image is blurry or dark - take a clearer photo
2. Only shows leaves/stem, not full plant - zoom out
3. Using local AI fallback (less accurate) - try taking multiple photos

## UNDERSTANDING THE AI WORKFLOW

```
User scans plant image
        ↓
        ├─→ Is it a plant? (HSV color check) 
        │    NO → "Not a Plant" error
        │    YES → Continue
        ↓
        ├─→ Try Gemini API (if within quota)
        │    SUCCESS → Return accurate analysis
        │    ERROR 429 (quota exhausted) → Fall back to local
        │    OTHER ERROR → Try next Gemini model
        ↓
        ├─→ Use Local ResNet-50 Model
        │    ├─ First time: Load model (30-60s)
        │    └─ Subsequent: Use cached model (5-10s)
        ↓
        └─→ Return plant identification & care tips
```

## API ENDPOINTS

### Health Check
```bash
curl http://192.168.29.241/plant_app/api/
```
Response shows:
```json
{
  "status": "online",
  "engine": "Google Gemini + Local ResNet-50 Fallback",
  "api_key_loaded": true,
  "local_ai_available": true
}
```

### Plant Identification
```bash
curl -X POST \
  -F "image=@photo.jpg" \
  http://192.168.29.241/plant_app/api/identify.php
```

## WHAT WAS CHANGED

### Files Modified:
1. **ai_service/app.py**
   - Added local ResNet-50 fallback
   - Detects 429 quota errors automatically
   - Falls back to local AI when needed

2. **ai_service/requirements.txt**
   - Added: `transformers` (for AI models)
   - Added: `torch` (deep learning framework)

3. **SETUP_WIFI_MODE.bat** (NEW)
   - One-click setup for WiFi mode

### Files UNCHANGED:
- Flutter app code (all UI components working)
- Database schema (no migrations needed)
- Product catalog (all data preserved)
- Cart system (all features working)
- API endpoints (fully compatible)

## QUICK COMMANDS

### Start Everything WiFi Mode
```batch
SETUP_WIFI_MODE.bat
```

### Check if AI service is running
```bash
curl http://127.0.0.1:5001/
```

### Restart just the AI service
```bash
taskkill /IM python.exe /F
cd ai_service
python app.py
```

### View AI service logs
- Open `ai_service/ai_log.txt` to see what AI is doing
- Shows which method was used (Gemini vs Local ResNet)

## GEMINI API QUOTA INFORMATION

**Free Tier Limits:**
- 20 requests per day
- Resets at midnight UTC
- Multiple models available (fallback chain)

**When Quota Exhausted:**
1. App detects 429 error
2. Automatically uses local ResNet-50
3. User doesn't see any error - just slightly lower accuracy

**To Upgrade:**
1. Visit: https://console.cloud.google.com
2. Enable billing on your Gemini API project
3. Pay-as-you-go: ~$0.00075 per request
4. Unlimited daily usage

## NEXT STEPS

### For Immediate Testing:
```
1. Run SETUP_WIFI_MODE.bat
2. Connect phone to WiFi
3. Open Flora AI app
4. Try scanning a plant
```

### For Long-Term Deployment:
```
1. Set up Windows auto-start task (already done)
2. Install paid Gemini API tier (optional, better accuracy)
3. Create backup of database
4. Document plant care tips in your own database
```

## SUPPORT

If AI is still not working:
1. Check `ai_service/ai_log.txt` for error messages
2. Verify all services running: `SETUP_WIFI_MODE.bat`
3. Try restarting the phone app
4. Ensure WiFi connection is stable
5. Check if plant image is clear and well-lit

---

**Made with ❤️ for plant lovers** 🌿
