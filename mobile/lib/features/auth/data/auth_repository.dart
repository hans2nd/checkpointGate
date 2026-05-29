import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../core/network/api_client.dart';

class AuthRepository {
  Dio get _dio => ApiClient.instance;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  /// Login using Employee ID only (no password)
  Future<Map<String, dynamic>> login(String employeeId) async {
    try {
      final response = await _dio.post('/login', data: {
        'employee_id': employeeId,
        'login_mode': 'employee',
        'device_name': 'android_flutter',
      });
      
      final body = response.data;
      if (body is Map<String, dynamic>) {
        // Laravel's response pattern wraps token inside `data`
        final token = body['data']?['token'] ?? body['token'];
        
        if (token != null) {
          await _storage.write(key: 'auth_token', value: token);
          final userName = body['data']?['user']?['name'];
          if (userName != null) {
            await _storage.write(key: 'user_name', value: userName);
          }
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

  Future<void> clearLocalData() async {
    await _storage.delete(key: 'auth_token');
    await _storage.delete(key: 'user_name');
  }

  Future<void> logout() async {
    try {
      await _dio.post('/logout');
    } catch (_) {
      // ignore
    } finally {
      await clearLocalData();
    }
  }

  Future<bool> checkAuth() async {
    final token = await _storage.read(key: 'auth_token');
    if (token == null) return false;

    try {
      final response = await _dio.get('/me');
      if (response.statusCode == 200) {
        final data = response.data;
        if (data is Map<String, dynamic>) {
          final userName = data['data']?['name'];
          if (userName != null) {
            await _storage.write(key: 'user_name', value: userName);
          }
        }
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
