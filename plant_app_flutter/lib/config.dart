class Config {
  // Primary local API (LAN)
  static const String baseUrl = "http://192.168.29.241/plant_app/api";

  // Optional public API URL (recommended for using mobile data / different Wi-Fi)
  // Example: "https://your-domain.com/plant_app/api" or ngrok/cloudflare tunnel URL.
  static const String publicBaseUrl = "";

  // Auto-fallback candidates checked by ApiService when connection fails.
  static const List<String> fallbackBaseUrls = [
    "http://HEER-DALAL/plant_app/api", // Windows hostname on LAN (IP-independent)
    "http://10.0.2.2/plant_app/api",   // Android emulator -> host machine
    "http://127.0.0.1/plant_app/api",  // Same-device testing
    "http://localhost/plant_app/api",  // Same-device testing
  ];

  static const String appName = "Flora AI";
}
































