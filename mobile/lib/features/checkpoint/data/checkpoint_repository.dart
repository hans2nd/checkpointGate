import 'package:dio/dio.dart';
import '../../../core/network/api_client.dart';

class CheckpointRepository {
  Dio get _dio => ApiClient.instance;

  Future<List<dynamic>> fetchActiveCheckpoints() async {
    try {
      // Ambil yang berstatus START (belum mulai)
      final responseStart = await _dio.get('/checkpoints', queryParameters: {
        'status': 'START',
        'per_page': 100, // Perbesar agar tidak miss data
      });
      
      // Ambil yang berstatus ON LOADING (sedang jalan)
      final responseLoading = await _dio.get('/checkpoints', queryParameters: {
        'status': 'ON LOADING',
        'per_page': 100,
      });

      final startList = responseStart.data['data'] as List<dynamic>? ?? [];
      final loadingList = responseLoading.data['data'] as List<dynamic>? ?? [];

      // Gabungkan, filter (hanya yang sudah ada waktu_penerimaan_dokumen), dan urutkan
      final combined = [...startList, ...loadingList].where((cp) => cp['waktu_penerimaan_dokumen'] != null).toList();
      
      combined.sort((a, b) {
        final dateA = DateTime.tryParse(a['waktu_penerimaan_dokumen'] ?? '') ?? DateTime(2000);
        final dateB = DateTime.tryParse(b['waktu_penerimaan_dokumen'] ?? '') ?? DateTime(2000);
        return dateB.compareTo(dateA); // descending
      });

      return combined;
    } catch (e) {
      if (e is DioException && e.response != null) {
         throw Exception(e.response?.data['message'] ?? 'Error fetching data');
      }
      throw Exception('Gagal memuat data: $e');
    }
  }

  Future<void> assignGate(int id, int gateNumber) async {
    try {
      await _dio.post('/checkpoints/$id/assign-gate', data: {'gate': gateNumber});
    } catch (e) {
      if (e is DioException && e.response != null) {
         throw Exception(e.response?.data['message'] ?? 'Error assigning gate');
      }
      throw Exception('Gagal menetapkan gate: $e');
    }
  }

  Future<List<dynamic>> getAvailableGates() async {
    try {
      final response = await _dio.get('/gates/available');
      return response.data['data'] as List<dynamic>? ?? [];
    } catch (e) {
      if (e is DioException && e.response != null) {
         throw Exception(e.response?.data['message'] ?? 'Error fetching gates');
      }
      throw Exception('Gagal memuat daftar gate: $e');
    }
  }

  Future<void> triggerStart(int id) async {
    try {
      await _dio.post('/checkpoints/$id/trigger-start');
    } catch (e) {
      if (e is DioException && e.response != null) {
         throw Exception(e.response?.data['message'] ?? 'Error triggering start');
      }
      throw Exception('Gagal memulai loading: $e');
    }
  }

  Future<void> triggerEnd(int id) async {
    try {
      await _dio.post('/checkpoints/$id/trigger-end');
    } catch (e) {
      if (e is DioException && e.response != null) {
         throw Exception(e.response?.data['message'] ?? 'Error triggering end');
      }
      throw Exception('Gagal mengakhiri loading: $e');
    }
  }
}
