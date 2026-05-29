import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:go_router/go_router.dart';
import '../providers/checkpoint_provider.dart';
import '../../home/providers/dashboard_provider.dart';

class CheckpointFormScreen extends ConsumerStatefulWidget {
  const CheckpointFormScreen({super.key});

  @override
  ConsumerState<CheckpointFormScreen> createState() => _CheckpointFormScreenState();
}

class _CheckpointFormScreenState extends ConsumerState<CheckpointFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nopolController = TextEditingController();
  final _vendorController = TextEditingController();
  final _driverController = TextEditingController();

  bool _isLoading = false;

  @override
  void dispose() {
    _nopolController.dispose();
    _vendorController.dispose();
    _driverController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final repo = ref.read(checkpointRepositoryProvider);
      await repo.createCheckpoint({
        'no_polisi': _nopolController.text.toUpperCase(),
        'vendor': _vendorController.text.toUpperCase(),
        'driver': _driverController.text.toUpperCase(),
      });

      if (mounted) {
        _nopolController.clear();
        _vendorController.clear();
        _driverController.clear();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Berhasil menginput data kedatangan!'), backgroundColor: Colors.green),
        );
        
        // Refresh data dashboard summary
        ref.invalidate(dashboardStatsProvider);
        
        if (context.canPop()) {
          context.pop();
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString().replaceAll('Exception: ', '')), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final today = DateFormat('dd MMM yyyy').format(DateTime.now());

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Card(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        elevation: 2,
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text(
                  'Vehicle Register',
                  style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.blue.shade900),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 24),
                TextFormField(
                  initialValue: today,
                  readOnly: true,
                  decoration: const InputDecoration(
                    labelText: 'Arrival',
                    border: OutlineInputBorder(),
                    filled: true,
                    fillColor: Color(0xFFF3F4F6),
                    prefixIcon: Icon(Icons.calendar_today),
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _nopolController,
                  textCapitalization: TextCapitalization.characters,
                  decoration: const InputDecoration(
                    labelText: 'Plate No/Nomor Polisi',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.directions_car),
                  ),
                  validator: (value) => value == null || value.trim().isEmpty ? 'Wajib diisi' : null,
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _vendorController,
                  textCapitalization: TextCapitalization.characters,
                  decoration: const InputDecoration(
                    labelText: 'Vendor',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.business),
                  ),
                  validator: (value) => value == null || value.trim().isEmpty ? 'Wajib diisi' : null,
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _driverController,
                  textCapitalization: TextCapitalization.characters,
                  decoration: const InputDecoration(
                    labelText: 'Driver / Supir',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.person),
                  ),
                  validator: (value) => value == null || value.trim().isEmpty ? 'Wajib diisi' : null,
                ),
                const SizedBox(height: 32),
                SizedBox(
                  height: 50,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF06B6D4), // Cyan 500
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    onPressed: _isLoading ? null : _submit,
                    child: _isLoading
                        ? const CircularProgressIndicator(color: Colors.white)
                        : const Text('Simpan Data', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
