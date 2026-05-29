import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../core/network/connection_provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../../checkpoint/presentation/checkpoint_list_screen.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final userNameAsync = ref.watch(userNameProvider);

    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Checkpoint Gate', style: TextStyle(fontSize: 18)),
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
            icon: const Icon(Icons.search),
            tooltip: 'Cari Checkpoint',
            onPressed: () {
              context.push('/search');
            },
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
          const Expanded(
            child: CheckpointListScreen(), // Shows the filtered active gates
          ),
        ],
      ),
    );
  }

  Widget _buildConnectionStatusBar(WidgetRef ref) {
    final status = ref.watch(connectionProvider);

    // Determine colors and icon based on connection state
    Color bgColor;
    IconData iconData;
    String displayText;
    bool showRefresh = false;

    if (status.message == 'Memeriksa koneksi...') {
      // Checking state
      bgColor = Colors.orange;
      iconData = Icons.sync;
      displayText = status.message;
    } else if (status.isConnected) {
      // Connected - green with ping info
      bgColor = Colors.green;
      iconData = Icons.wifi;
      displayText = '${status.message} • ${status.pingMs}ms';
    } else {
      // Disconnected - red with refresh hint
      bgColor = Colors.red.shade700;
      iconData = Icons.wifi_off;
      displayText = status.message;
      showRefresh = true;
    }

    return GestureDetector(
      onTap: () {
        // Allow tapping to manually refresh connection
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
            // Animated icon for checking state
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
