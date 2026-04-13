import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../auth/providers/auth_provider.dart';
import '../../checkpoint/presentation/checkpoint_list_screen.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('GIIC Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.qr_code_scanner),
            onPressed: () {
              context.push('/scanner');
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
          _buildConnectionStatusBar(),
          const Expanded(
            child: CheckpointListScreen(), // Shows the filtered active gates
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // TODO: Add new pipeline
        },
        backgroundColor: const Color(0xFFF97316),
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildConnectionStatusBar() {
    return Container(
      width: double.infinity,
      color: Colors.green, // Change color based on Connectivity Stream later
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 16),
      child: const Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.wifi, size: 14, color: Colors.white),
          SizedBox(width: 8),
          Text(
            'Online - Tersambung',
            style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
          )
        ],
      ),
    );
  }
}
