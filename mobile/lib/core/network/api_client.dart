import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../config/app_config.dart';

class ApiClient {
  static final Dio _dio = Dio();
  static const FlutterSecureStorage _storage = FlutterSecureStorage();

  static Dio get instance {
    _dio.options.baseUrl = AppConfig.baseUrl;
    _dio.options.connectTimeout = const Duration(seconds: 10);
    _dio.options.receiveTimeout = const Duration(seconds: 10);
    _dio.options.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    _dio.interceptors.clear(); // Ensure no duplicate interceptors
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          // Check for token and attach
          final token = await _storage.read(key: 'auth_token');
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) async {
          // Handle 401 Unauthorized globally if needed
          return handler.next(e);
        },
      ),
    );

    return _dio;
  }

  static Future<bool> isConnected() async {
    final connectivityResult = await (Connectivity().checkConnectivity());
    if (connectivityResult.contains(ConnectivityResult.none)) {
      return false;
    }
    return true;
  }
}
