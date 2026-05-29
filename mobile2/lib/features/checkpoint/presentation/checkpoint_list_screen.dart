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
        _showResultDialog(
          context,
          title: 'Berhasil!',
          message: action == 'START' ? 'Loading berhasil dimulai.' : 'Loading berhasil diselesaikan.',
          isError: false,
        );
      }
    } catch (e) {
      if (context.mounted) Navigator.pop(context); // close loading overlay
      if (context.mounted) {
        _showResultDialog(
          context,
          title: 'Gagal!',
          message: e.toString().replaceAll('Exception: ', ''),
          isError: true,
        );
      }
    }
  }

  Future<void> _showGateSelectionDialog(BuildContext context, WidgetRef ref, int checkpointId, String jenisBarang) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (c) => const Center(child: CircularProgressIndicator()),
    );

    try {
      final repo = ref.read(checkpointRepositoryProvider);
      final allGates = await repo.getAvailableGates();

      final targetJenis = (jenisBarang == 'FROZEN' || jenisBarang == 'CHILLED') ? 'FROZEN' : 'DRY';
      final gates = allGates.where((g) => g['jenis_barang'] == targetJenis).toList();

      if (context.mounted) Navigator.pop(context); // close loading overlay

      if (context.mounted) {
        showModalBottomSheet(
          context: context,
          isScrollControlled: true,
          shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
          builder: (ctx) {
            return DraggableScrollableSheet(
              expand: false,
              initialChildSize: 0.6,
              maxChildSize: 0.9,
              builder: (ctx, scrollController) {
                return Column(
                  children: [
                    const Padding(
                      padding: EdgeInsets.all(16.0),
                      child: Text('Pilih Gate', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                    ),
                    Expanded(
                      child: GridView.builder(
                        controller: scrollController,
                        padding: const EdgeInsets.all(16),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 4,
                          crossAxisSpacing: 8,
                          mainAxisSpacing: 8,
                        ),
                        itemCount: gates.length,
                        itemBuilder: (context, index) {
                          final gate = gates[index];
                          final isAvailable = gate['available'] == true;
                          final gateName = gate['display_name'] ?? (gate['jenis_barang'] == 'FROZEN' ? 'F-${gate['nomor']}' : 'D-${gate['nomor'] - 16}');

                          return InkWell(
                            onTap: isAvailable ? () async {
                              Navigator.pop(ctx);
                              await _handleAssignGate(context, ref, checkpointId, gate['nomor']);
                            } : null,
                            child: Container(
                              decoration: BoxDecoration(
                                color: isAvailable ? Colors.green.shade100 : Colors.grey.shade300,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: isAvailable ? Colors.green : Colors.grey),
                              ),
                              alignment: Alignment.center,
                              child: Text(
                                gateName,
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: isAvailable ? Colors.green.shade900 : Colors.grey.shade600,
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                );
              },
            );
          },
        );
      }
    } catch (e) {
      if (context.mounted) Navigator.pop(context); // close loading overlay
      if (context.mounted) {
        _showResultDialog(context, title: 'Gagal', message: e.toString(), isError: true);
      }
    }
  }

  Future<void> _handleAssignGate(BuildContext context, WidgetRef ref, int id, int gateNumber) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (c) => const Center(child: CircularProgressIndicator()),
    );
    try {
      final repo = ref.read(checkpointRepositoryProvider);
      await repo.assignGate(id, gateNumber);

      if (context.mounted) Navigator.pop(context);
      ref.invalidate(activeCheckpointsProvider);

      if (context.mounted) {
        _showResultDialog(context, title: 'Berhasil', message: 'Gate $gateNumber ditetapkan.', isError: false);
      }
    } catch (e) {
      if (context.mounted) Navigator.pop(context);
      if (context.mounted) {
        _showResultDialog(context, title: 'Gagal', message: e.toString().replaceAll('Exception: ', ''), isError: true);
      }
    }
  }

  void _showResultDialog(BuildContext context, {required String title, required String message, required bool isError}) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        icon: Icon(
          isError ? Icons.error_outline : Icons.check_circle_outline,
          color: isError ? Colors.red : Colors.green,
          size: 48,
        ),
        title: Text(title, style: TextStyle(color: isError ? Colors.red : Colors.green, fontWeight: FontWeight.bold)),
        content: Text(message, textAlign: TextAlign.center),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('OK'),
          ),
        ],
      ),
    );
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

              String displayStatus = status;
              if (item['gate'] == null) {
                displayStatus = 'Assign gate';
              } else if (status == 'START') {
                displayStatus = 'Ready';
              } else if (status == 'ON LOADING') {
                displayStatus = 'On Loading';
              }

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
                        'Kedatangan: $tanggal',
                        style: const TextStyle(color: Colors.grey),
                      ),
                      Text('Driver: ${item['driver'] ?? '-'}', style: const TextStyle(color: Colors.grey)),
                      Text('Vendor: ${item['vendor'] ?? '-'} (${item['jenis_barang'] ?? '-'})', style: const TextStyle(color: Colors.grey, fontSize: 12)),
                      const SizedBox(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Status: $displayStatus', style: const TextStyle(fontWeight: FontWeight.w500)),
                          Text(timeIn, style: const TextStyle(color: Colors.grey)),
                        ],
                      ),
                      const Divider(height: 24),
                      Row(
                        children: [
                          if (item['gate'] == null)
                            Expanded(
                              child: ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.orange,
                                  foregroundColor: Colors.white,
                                ),
                                onPressed: () {
                                  _showGateSelectionDialog(context, ref, item['id'], item['jenis_barang'] ?? '');
                                },
                                icon: const Icon(Icons.assignment),
                                label: const Text('Assign Gate'),
                              ),
                            )
                          else ...[
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
                            if (!isOnLoading && status == 'START') ...[
                              const SizedBox(width: 8),
                              OutlinedButton.icon(
                                onPressed: () {
                                  _showGateSelectionDialog(context, ref, item['id'], item['jenis_barang'] ?? '');
                                },
                                icon: const Icon(Icons.edit, size: 16),
                                label: const Text('Ubah'),
                                style: OutlinedButton.styleFrom(
                                  foregroundColor: Colors.orange,
                                  side: const BorderSide(color: Colors.orange),
                                ),
                              ),
                            ],
                          ],
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
