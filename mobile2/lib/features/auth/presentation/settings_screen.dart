import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import '../../../core/config/app_config.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  final _urlCtrl = TextEditingController();
  bool _isTesting = false;
  String _testResult = '';
  Color _testColor = Colors.grey;

  @override
  void initState() {
    super.initState();
    _urlCtrl.text = AppConfig.baseUrl.replaceAll('/api', '');
  }

  Future<void> _testConnection() async {
    setState(() {
      _isTesting = true;
      _testResult = 'Menguji koneksi...';
      _testColor = Colors.grey;
    });

    try {
      // Build test URL
      String apiUrl = _urlCtrl.text.trim();
      if (apiUrl.endsWith('/')) {
        apiUrl = apiUrl.substring(0, apiUrl.length - 1);
      }
      if (!apiUrl.endsWith('/api')) {
        apiUrl = '$apiUrl/api';
      }

      // Gunakan Dio baru untuk test, jangan ganggu instance utama
      final testDio = Dio(BaseOptions(
        baseUrl: apiUrl,
        connectTimeout: const Duration(seconds: 5),
        receiveTimeout: const Duration(seconds: 5),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      ));

      final response = await testDio.post('/login', data: {
        'email': 'connection_test@test.com',
        'password': 'test',
        'device_name': 'connection_test',
      });

      // Jika dapat respon JSON, koneksi berhasil (meski login salah)
      if (response.data is Map) {
        setState(() {
          _testResult = '✅ Koneksi berhasil! Server merespon dengan benar.';
          _testColor = Colors.green;
        });
      }
    } on DioException catch (e) {
      if (e.response != null) {
        // Server merespon (401/422 = koneksi OK, hanya autentikasi gagal)
        setState(() {
          _testResult = '✅ Koneksi berhasil! Server merespon (status ${e.response?.statusCode}).';
          _testColor = Colors.green;
        });
      } else {
        setState(() {
          _testResult = '❌ Gagal terhubung ke server.\n${e.message ?? "Periksa URL dan pastikan server berjalan."}';
          _testColor = Colors.red;
        });
      }
    } catch (e) {
      setState(() {
        _testResult = '❌ Error: $e';
        _testColor = Colors.red;
      });
    } finally {
      setState(() => _isTesting = false);
    }
  }

  Future<void> _saveConfig() async {
    final url = _urlCtrl.text.trim();
    if (url.isEmpty || !url.startsWith('http')) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('URL tidak valid. Harus diawali http:// atau https://')),
      );
      return;
    }

    await AppConfig.setBaseUrl(url);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('✅ Konfigurasi URL Berhasil Disimpan'),
          backgroundColor: Colors.green,
        ),
      );
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pengaturan API Server')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Info box
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.blue.shade50,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.blue.shade200),
              ),
              child: const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('ℹ️ Petunjuk URL:', style: TextStyle(fontWeight: FontWeight.bold)),
                  SizedBox(height: 4),
                  Text('• Emulator Android: http://10.0.2.2:PORT/path', style: TextStyle(fontSize: 12)),
                  Text('• Device Fisik (WiFi sama): http://IP_PC:PORT/path', style: TextStyle(fontSize: 12)),
                  Text('• Dev (Laragon 8082): http://10.0.2.2:8082/checkpoint-GIIC', style: TextStyle(fontSize: 12)),
                  Text('• Prod (port 8085): http://IP_SERVER:8085/checkpoint-GIIC', style: TextStyle(fontSize: 12)),
                ],
              ),
            ),
            const SizedBox(height: 20),
            const Text('Backend Host Address (Laravel Endpoint):', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextFormField(
              controller: _urlCtrl,
              decoration: const InputDecoration(
                hintText: 'http://10.0.2.2:8082/checkpoint-GIIC',
                helperText: 'Masukkan hostname/IP tujuan tanpa akhiran /api',
                border: OutlineInputBorder(),
              ),
              keyboardType: TextInputType.url,
            ),
            const SizedBox(height: 8),
            // Current active URL display
            Text(
              'URL aktif saat ini: ${AppConfig.baseUrl}',
              style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
            ),
            const SizedBox(height: 16),
            // Test result
            if (_testResult.isNotEmpty)
              Container(
                padding: const EdgeInsets.all(12),
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: _testColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: _testColor.withValues(alpha: 0.3)),
                ),
                child: Text(_testResult, style: TextStyle(color: _testColor, fontSize: 13)),
              ),
            // Buttons
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: _isTesting ? null : _testConnection,
                    icon: _isTesting
                        ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2))
                        : const Icon(Icons.wifi_find),
                    label: const Text('Test Koneksi'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: _saveConfig,
                    icon: const Icon(Icons.save),
                    label: const Text('Simpan'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
