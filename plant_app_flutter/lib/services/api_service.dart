import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config.dart';

class ApiService {
  static const String _cacheKey = 'active_api_base_url';
  static String? _activeBaseUrl;
  static Future<String>? _resolvingFuture;

  static String get baseUrl => _activeBaseUrl ?? Config.baseUrl;

  static Future<String> _resolveBaseUrl({bool forceRefresh = false}) async {
    if (!forceRefresh && _activeBaseUrl != null) {
      return _activeBaseUrl!;
    }

    if (!forceRefresh && _resolvingFuture != null) {
      return _resolvingFuture!;
    }

    _resolvingFuture = _probeBaseUrl();
    final resolved = await _resolvingFuture!;
    _activeBaseUrl = resolved;
    _resolvingFuture = null;
    return resolved;
  }

  static Future<String> _probeBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString(_cacheKey);

    final candidates = <String>[];
    if (cached != null && cached.isNotEmpty) {
      candidates.add(cached);
    }
    if (Config.publicBaseUrl.isNotEmpty) {
      candidates.add(Config.publicBaseUrl);
    }
    candidates.add(Config.baseUrl);
    candidates.addAll(Config.fallbackBaseUrls);

    final seen = <String>{};
    for (final candidate in candidates) {
      final url = candidate.trim();
      if (url.isEmpty || seen.contains(url)) continue;
      seen.add(url);

      final ok = await _isReachable(url);
      if (ok) {
        await prefs.setString(_cacheKey, url);
        return url;
      }
    }

    return Config.baseUrl;
  }

  static Future<bool> _isReachable(String url) async {
    try {
      final res = await http
          .get(Uri.parse('$url/products.php'))
          .timeout(const Duration(seconds: 4));
      return res.statusCode >= 200 && res.statusCode < 500;
    } catch (_) {
      return false;
    }
  }

  static Future<Map<String, dynamic>> _withRetry(
    Future<Map<String, dynamic>> Function(String baseUrl) fn,
  ) async {
    final firstBase = await _resolveBaseUrl();
    try {
      return await fn(firstBase);
    } catch (e) {
      final retryBase = await _resolveBaseUrl(forceRefresh: true);
      if (retryBase != firstBase) {
        return await fn(retryBase);
      }
      throw Exception('Network error: $e');
    }
  }

  // Generic GET request
  static Future<Map<String, dynamic>> get(String endpoint) async {
    return _withRetry((base) async {
      final response = await http
          .get(Uri.parse('$base/$endpoint'))
          .timeout(const Duration(seconds: 15));
      return _processResponse(response);
    });
  }

  // Generic POST request
  static Future<Map<String, dynamic>> post(String endpoint, Map<String, dynamic> data) async {
    return _withRetry((base) async {
      final response = await http.post(
        Uri.parse('$base/$endpoint'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode(data),
      ).timeout(const Duration(seconds: 15));
      return _processResponse(response);
    });
  }

  // Multipart POST request (for image uploads)
  static Future<Map<String, dynamic>> postMultipart(
      String endpoint, Map<String, String> fields, String filePath, String fileField) async {
    return _withRetry((base) async {
      var request = http.MultipartRequest('POST', Uri.parse('$base/$endpoint'));

      request.fields.addAll(fields);
      request.files.add(await http.MultipartFile.fromPath(fileField, filePath));

      var streamedResponse = await request.send().timeout(const Duration(seconds: 180));
      var response = await http.Response.fromStream(streamedResponse);

      return _processResponse(response);
    });
  }

  static Map<String, dynamic> _processResponse(http.Response response) {
    if (response.statusCode == 200 || response.statusCode == 201) {
      try {
        final data = jsonDecode(response.body);
        return data is Map<String, dynamic> ? data : {'data': data};
      } catch (e) {
        // Sometimes APIs return plain text on error instead of JSON
        if (response.body.toLowerCase().contains('fatal error') || 
            response.body.toLowerCase().contains('syntax error')) {
          throw Exception('Server PHP error: ${response.body}');
        }
        throw Exception('Invalid response format. Raw body: ${response.body}');
      }
    } else {
      throw Exception('Server error: ${response.statusCode}');
    }
  }
}
