import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../core/network/api_client.dart';

class AuthRepository {
  final Dio _dio = ApiClient.instance;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await _dio.post('/login', data: {
        'email': email,
        'password': password,
        'device_name': 'android_flutter',
      });
      
      final data = response.data;
      if (data['token'] != null) {
        await _storage.write(key: 'auth_token', value: data['token']);
      }
      return data;
    } on DioException catch (e) {
      if (e.response != null) {
        throw Exception(e.response?.data['message'] ?? 'Login gagal.');
      } else {
        throw Exception('Tidak ada koneksi ke server.');
      }
    }
  }

  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (_) {
      // ignore
    } finally {
      await _storage.delete(key: 'auth_token');
    }
  }

  Future<bool> checkAuth() async {
    final token = await _storage.read(key: 'auth_token');
    if (token == null) return false;

    try {
      final response = await _dio.get('/me');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
