import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

final dashboardRepositoryProvider = Provider((ref) => DashboardRepository());

final dashboardStatsProvider = FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final repo = ref.watch(dashboardRepositoryProvider);
  return await repo.fetchStats();
});

class DashboardRepository {
  Dio get _dio => ApiClient.instance;

  Future<Map<String, dynamic>> fetchStats() async {
    try {
      final response = await _dio.get('/dashboard');
      return response.data['data']['stats'] as Map<String, dynamic>;
    } catch (e) {
      if (e is DioException && e.response != null) {
        throw Exception(e.response?.data['message'] ?? 'Error fetching stats');
      }
      throw Exception('Gagal memuat dashboard: $e');
    }
  }
}
