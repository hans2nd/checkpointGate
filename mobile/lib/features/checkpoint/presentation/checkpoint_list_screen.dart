import 'package:flutter/material.dart';

class CheckpointListScreen extends StatefulWidget {
  const CheckpointListScreen({super.key});

  @override
  State<CheckpointListScreen> createState() => _CheckpointListScreenState();
}

class _CheckpointListScreenState extends State<CheckpointListScreen> {
  // Mock data for UI demonstration until API integration is complete
  final List<Map<String, dynamic>> _activeGates = [
    {
      'id': 1,
      'no_polisi': 'B 1234 CD',
      'driver': 'Budi Santoso',
      'gate': 'F-1',
      'status': 'Terima Dokumen IN',
      'time_in': '10:30',
    },
    {
      'id': 2,
      'no_polisi': 'D 5678 EF',
      'driver': 'Asep Ujang',
      'gate': 'D-3',
      'status': 'On Loading',
      'time_in': '11:15',
    }
  ];

  @override
  Widget build(BuildContext context) {
    if (_activeGates.isEmpty) {
      return const Center(
        child: Text('Tidak ada gate yang aktif saat ini.', style: TextStyle(color: Colors.grey)),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _activeGates.length,
      itemBuilder: (context, index) {
        final item = _activeGates[index];
        bool isOnLoading = item['status'] == 'On Loading';

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
                      item['no_polisi'],
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      decoration: BoxDecoration(
                        color: isOnLoading ? Colors.amber.shade100 : Colors.blue.shade100,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Text(
                        'Gate ${item['gate']}',
                        style: TextStyle(
                            color: isOnLoading ? Colors.amber.shade900 : Colors.blue.shade900,
                            fontWeight: FontWeight.bold),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Text('Driver: ${item['driver']}', style: const TextStyle(color: Colors.grey)),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Status: ${item['status']}', style: const TextStyle(fontWeight: FontWeight.w500)),
                    Text(item['time_in'], style: const TextStyle(color: Colors.grey)),
                  ],
                ),
                const Divider(height: 24),
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton.icon(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: isOnLoading ? Colors.green : const Color(0xFF06B6D4), // Cyan-500
                        ),
                        onPressed: () {
                          // TODO: Implement Offline-Queue Action
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
    );
  }
}
