import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/checkpoint_provider.dart';
import 'package:intl/intl.dart';

class CheckpointListScreen extends ConsumerWidget {
  const CheckpointListScreen({super.key});

  Future<void> _handleAction(BuildContext context, WidgetRef ref, int id, String action) async {
    final repo = ref.read(checkpointRepositoryProvider);

    // Show loading overlay
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (c) => const Center(child: CircularProgressIndicator()),
    );

    try {
      if (action == 'START') {
        await repo.triggerStart(id);
      } else {
        await repo.triggerEnd(id);
      }

      // Close loading overlay
      if (context.mounted) Navigator.pop(context);

      // Refresh list
      ref.invalidate(activeCheckpointsProvider);

      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(action == 'START' ? 'Pemuatan dimulai!' : 'Pemuatan selesai!'),
            backgroundColor: Colors.green,
          ),
        );
      }
    } catch (e) {
      if (context.mounted) Navigator.pop(context); // close loading overlay
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
           SnackBar(content: Text(e.toString().replaceAll('Exception: ', '')), backgroundColor: Colors.red),
        );
      }
    }
  }

  String _formatTime(String? dateStr) {
    if (dateStr == null) return '-';
    try {
      final date = DateTime.parse(dateStr).toLocal();
      return DateFormat('HH:mm').format(date);
    } catch (_) {
      return dateStr;
    }
  }

  String _formatDate(String? dateStr) {
    if (dateStr == null) return '-';
    try {
      final date = DateTime.parse(dateStr).toLocal();
      return DateFormat('dd MMM yyyy', 'id_ID').format(date);
    } catch (_) {
      return dateStr;
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final asyncData = ref.watch(activeCheckpointsProvider);

    return asyncData.when(
      loading: () => const Center(child: CircularProgressIndicator()),
      error: (error, stack) => Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text('Gagal memuat: $error', style: const TextStyle(color: Colors.red), textAlign: TextAlign.center),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: () => ref.invalidate(activeCheckpointsProvider),
              child: const Text('Coba Lagi'),
            )
          ],
        ),
      ),
      data: (checkpoints) {
        if (checkpoints.isEmpty) {
          return const Center(
            child: Text('Tidak ada gate yang aktif saat ini.', style: TextStyle(color: Colors.grey)),
          );
        }

        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(activeCheckpointsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: checkpoints.length,
            itemBuilder: (context, index) {
              final item = checkpoints[index];
              final status = item['status'] ?? '';
              final isOnLoading = status == 'ON LOADING';
              final timeIn = _formatTime(item['waktu_penerimaan_dokumen']);
              final tanggal = _formatDate(item['tanggal']);

              String gateDisplay = '';
              if (item['gate'] != null) {
   int g = int.parse(item['gate'].toString());
   gateDisplay = g <= 16 ? 'F-$g' : 'D-${g-16}';
}

              return Card(
                elevation: 2,
                margin: const EdgeInsets.only(bottom: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            item['no_polisi'] ?? '-',
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                          ),
                          if (gateDisplay.isNotEmpty)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                              decoration: BoxDecoration(
                                color: isOnLoading ? Colors.amber.shade100 : Colors.blue.shade100,
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                'Gate $gateDisplay',
                                style: TextStyle(
                                    color: isOnLoading ? Colors.amber.shade900 : Colors.blue.shade900,
                                    fontWeight: FontWeight.bold),
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Tanggal: $tanggal',
                        style: const TextStyle(color: Colors.grey),
                      ),
                      Text('Driver: ${item['driver'] ?? '-'}', style: const TextStyle(color: Colors.grey)),
                      Text('Vendor: ${item['vendor'] ?? '-'}', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                      const SizedBox(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Status: $status', style: const TextStyle(fontWeight: FontWeight.w500)),
                          Text(timeIn, style: const TextStyle(color: Colors.grey)),
                        ],
                      ),
                      const Divider(height: 24),
                      Row(
                        children: [
                          Expanded(
                            child: ElevatedButton.icon(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: isOnLoading ? Colors.green : const Color(0xFF06B6D4), // Cyan-500
                                foregroundColor: Colors.white,
                              ),
                              onPressed: () {
                                _handleAction(context, ref, item['id'], isOnLoading ? 'END' : 'START');
                              },
                              icon: Icon(isOnLoading ? Icons.check_circle : Icons.play_arrow),
                              label: Text(isOnLoading ? 'End Loading' : 'Start Loading'),
                            ),
                          ),
                        ],
                      )
                    ],
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }
}
