import 'package:shared_preferences/shared_preferences.dart';

class AppConfig {
  static const String keyBaseUrl = 'API_BASE_URL';

  // Default URL untuk Android Emulator ke Laragon port 8082
  // 10.0.2.2 = localhost PC dari perspective Android Emulator
  // Untuk device fisik, ganti via Settings di app
  static String _baseUrl = 'http://192.168.15.19:8082/checkpoint-GIIC/public/api';

  static String get baseUrl => _baseUrl;

  static Future<void> loadConfig() async {
    final prefs = await SharedPreferences.getInstance();
    final savedUrl = prefs.getString(keyBaseUrl);
    if (savedUrl != null && savedUrl.isNotEmpty) {
      _baseUrl = savedUrl;
    }
  }

  static Future<void> setBaseUrl(String url) async {
    // Ensure URL doesn't end with a slash for safety
    if (url.endsWith('/')) {
      url = url.substring(0, url.length - 1);
    }

    // Add /api if not present
    if (!url.endsWith('/api')) {
      url = '$url/api';
    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(keyBaseUrl, url);
    _baseUrl = url;
  }

  /// Reset ke default (hapus URL tersimpan)
  static Future<void> resetBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(keyBaseUrl);
    _baseUrl = 'http://10.0.2.2:8082/checkpoint-GIIC/api';
  }
}
