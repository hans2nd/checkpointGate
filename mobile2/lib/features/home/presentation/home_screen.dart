import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/connection_provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final userNameAsync = ref.watch(userNameProvider);
    final dashboardStats = ref.watch(dashboardStatsProvider);

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('PL Checkpoint', style: TextStyle(fontSize: 18)),
            userNameAsync.when(
              data: (name) => Text(
                'Halo, ${name ?? 'User'}',
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.normal,
                ),
              ),
              loading: () => const SizedBox.shrink(),
              error: (_, __) => const SizedBox.shrink(),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => ref.refresh(dashboardStatsProvider),
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => ref.read(authControllerProvider.notifier).logout(),
          ),
        ],
      ),
      body: Column(
        children: [
          _buildConnectionStatusBar(ref),
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async {
                ref.refresh(dashboardStatsProvider);
              },
              child: ListView(
                padding: const EdgeInsets.all(16.0),
                children: [
                  const Text(
                    'Today Summary',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 16),
                  dashboardStats.when(
                    data: (stats) {
                      return GridView.count(
                        crossAxisCount: 2,
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        crossAxisSpacing: 16,
                        mainAxisSpacing: 16,
                        childAspectRatio: 1.1,
                        children: [
                          _buildStatCard(
                            'Parking',
                            stats['parking']?.toString() ?? '0',
                            Colors.orange,
                            Icons.local_parking,
                          ),
                          _buildStatCard(
                            'Loading/Unloading',
                            stats['on_loading']?.toString() ?? '0',
                            Colors.blue,
                            Icons.sync,
                          ),
                          _buildStatCard(
                            'Finish',
                            stats['finish']?.toString() ?? '0',
                            Colors.green,
                            Icons.check_circle,
                          ),
                          _buildStatCard(
                            'Total Vehicle',
                            stats['total_all']?.toString() ?? '0',
                            Colors.grey.shade700,
                            Icons.input,
                          ),
                        ],
                      );
                    },
                    loading: () => const Center(
                      child: Padding(
                        padding: EdgeInsets.all(32.0),
                        child: CircularProgressIndicator(),
                      ),
                    ),
                    error: (err, _) => Center(
                      child: Text('Gagal memuat data: $err', style: const TextStyle(color: Colors.red)),
                    ),
                  ),
                  const SizedBox(height: 32),
                  ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF06B6D4),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    icon: const Icon(Icons.add),
                    label: const Text('Input Kedatangan Kendaraan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    onPressed: () {
                      context.push('/form');
                    },
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, Color color, IconData icon) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Row(
              children: [
                Icon(icon, size: 20, color: color),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    title,
                    style: TextStyle(
                      color: Colors.grey.shade600,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildConnectionStatusBar(WidgetRef ref) {
    final status = ref.watch(connectionProvider);

    Color bgColor;
    IconData iconData;
    String displayText;
    bool showRefresh = false;

    if (status.message == 'Memeriksa koneksi...') {
      bgColor = Colors.orange;
      iconData = Icons.sync;
      displayText = status.message;
    } else if (status.isConnected) {
      bgColor = Colors.green;
      iconData = Icons.wifi;
      displayText = '${status.message} • ${status.pingMs}ms';
    } else {
      bgColor = Colors.red.shade700;
      iconData = Icons.wifi_off;
      displayText = status.message;
      showRefresh = true;
    }

    return GestureDetector(
      onTap: () {
        ref.read(connectionProvider.notifier).restart();
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        width: double.infinity,
        color: bgColor,
        padding: const EdgeInsets.symmetric(vertical: 5, horizontal: 16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            if (status.message == 'Memeriksa koneksi...')
              const SizedBox(
                width: 14,
                height: 14,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  color: Colors.white,
                ),
              )
            else
              Icon(iconData, size: 14, color: Colors.white),
            const SizedBox(width: 8),
            Text(
              displayText,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 12,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (showRefresh) ...[
              const SizedBox(width: 8),
              const Icon(Icons.refresh, size: 14, color: Colors.white70),
              const SizedBox(width: 2),
              const Text(
                'Tap untuk refresh',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 10,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
