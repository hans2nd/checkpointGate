import 'package:shared_preferences/shared_preferences.dart';

class AppConfig {
  static const String keyBaseUrl = 'API_BASE_URL';
  
  // Default URL is pointing to standard Android emulator loopback for localhost,
  // or a fallback development IP. User should configure this in the UI.
  static String _baseUrl = 'http://10.0.2.2/checkpoint-GIIC/api';

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
}
