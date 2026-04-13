import 'package:flutter/material.dart';
import '../../../core/config/app_config.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen> {
  final _urlCtrl = TextEditingController();

  @override
  void initState() {
    super.initState();
    _urlCtrl.text = AppConfig.baseUrl.replaceAll('/api', '');
  }

  Future<void> _saveConfig() async {
    final url = _urlCtrl.text.trim();
    if (url.isEmpty || !url.startsWith('http')) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('URL tidak valid. Harus diawali http:// atau https://')));
      return;
    }
    
    await AppConfig.setBaseUrl(url);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Konfigurasi URL Berhasil Disimpan')));
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pengaturan API Server')),
      body: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text('Backend Host Address (Laravel Endpoint):', style: TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextFormField(
              controller: _urlCtrl,
              decoration: const InputDecoration(
                hintText: 'http://192.168.1.10/checkpoint-GIIC',
                helperText: 'Masukkan hostname/IP tujuan tanpa akhiran /api',
              ),
              keyboardType: TextInputType.url,
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: _saveConfig,
              child: const Text('Simpan Pengaturan'),
            )
          ],
        ),
      ),
    );
  }
}
