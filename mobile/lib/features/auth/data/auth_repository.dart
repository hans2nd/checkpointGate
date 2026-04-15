import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../core/network/api_client.dart';

class AuthRepository {
  Dio get _dio => ApiClient.instance;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await _dio.post('/login', data: {
        'email': email,
        'password': password,
        'device_name': 'android_flutter',
      });
      
      final body = response.data;
      if (body is Map<String, dynamic>) {
        // Laravel's response pattern wraps token inside `data`
        final token = body['data']?['token'] ?? body['token'];
        
        if (token != null) {
          await _storage.write(key: 'auth_token', value: token);
          return body;
        }
      }
      throw Exception('Format respon tidak sesuai. Pastikan API mengembalikan Token yang valid.');
    } on DioException catch (e) {
      if (e.response != null) {
        final respData = e.response?.data;
        if (respData is Map<String, dynamic> && respData['message'] != null) {
          throw Exception(respData['message']);
        }
        throw Exception('Server merespon dengan error ${e.response?.statusCode}. Pastikan URL Endpoint API benar.');
      } else {
        throw Exception('Tidak ada koneksi ke server. Periksa Base URL (saat ini: ${_dio.options.baseUrl}).');
      }
    } catch (e) {
      throw Exception('Terjadi kesalahan tidak terduga: $e');
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
