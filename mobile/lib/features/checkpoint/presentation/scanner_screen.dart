import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/checkpoint_provider.dart';

class ScannerScreen extends ConsumerStatefulWidget {
  const ScannerScreen({super.key});

  @override
  ConsumerState<ScannerScreen> createState() => _ScannerScreenState();
}

class _ScannerScreenState extends ConsumerState<ScannerScreen> {
  final MobileScannerController _controller = MobileScannerController(
    formats: const [BarcodeFormat.all],
  );

  bool _isScanned = false;

  void _onDetect(BarcodeCapture capture) {
    if (_isScanned) return;

    final List<Barcode> barcodes = capture.barcodes;
    if (barcodes.isNotEmpty) {
      final String? code = barcodes.first.rawValue;
      if (code != null) {
        setState(() => _isScanned = true);
        _controller.stop();
        _showProcessDialog(code);
      }
    }
  }

  void _showProcessDialog(String scannedData) {
    // Mengekstrak angka dari hasil scan (misal 'F-1', 'Gate 1', atau '1' menjadi '1')
    final gateMatch = RegExp(r'\d+').firstMatch(scannedData);
    final gateNumber = gateMatch?.group(0);

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        title: const Text('Gate Terdeteksi'),
        content: Text('Gate Scan: $scannedData\n\nEksekusi Start Loading untuk gate ini?'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              setState(() => _isScanned = false);
              _controller.start();
            },
            child: const Text('Batal', style: TextStyle(color: Colors.grey)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
            onPressed: () async {
              Navigator.pop(ctx); // Tutup dialog konfirmasi
              
              if (gateNumber == null) {
                if (!context.mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Format QR Code Gate tidak valid.'), backgroundColor: Colors.red),
                );
                context.pop();
                return;
              }

              // Capture references before await to avoid async gap lint issues
              final scaffoldMessenger = ScaffoldMessenger.of(context);
              final router = GoRouter.of(context);
              final navigator = Navigator.of(context);

              // Tampilkan dialog loading
              showDialog(
                context: context,
                barrierDismissible: false,
                builder: (c) => const Center(child: CircularProgressIndicator()),
              );

              try {
                final repo = ref.read(checkpointRepositoryProvider);
                final checkpoints = await repo.fetchActiveCheckpoints();

                // Cari checkpoint di gate ini yang statusnya START
                final cp = checkpoints.cast<Map<String, dynamic>>().firstWhere(
                  (element) => element['gate'].toString() == gateNumber && element['status'] == 'START',
                  orElse: () => <String, dynamic>{},
                );

                if (cp.isNotEmpty && cp['id'] != null) {
                  await repo.triggerStart(cp['id']);
                  ref.invalidate(activeCheckpointsProvider);
                  
                  navigator.pop(); // Tutup loading
                  scaffoldMessenger.showSnackBar(
                    SnackBar(content: Text('Start Loading di Gate $gateNumber berhasil!'), backgroundColor: Colors.green),
                  );
                  router.pop(); // Kembali ke home
                } else {
                  navigator.pop(); // Tutup loading
                  scaffoldMessenger.showSnackBar(
                    SnackBar(content: Text('Tidak ada kendaraan dengan status START di Gate $gateNumber saat ini.'), backgroundColor: Colors.orange),
                  );
                  router.pop(); // Kembali ke home
                }
              } catch (e) {
                navigator.pop(); // Tutup loading
                scaffoldMessenger.showSnackBar(
                  SnackBar(content: Text('Terjadi kesalahan: ${e.toString().replaceAll('Exception: ', '')}'), backgroundColor: Colors.red),
                );
                router.pop(); // Kembali ke home
              }
            },
            child: const Text('START LOADING'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Scan Gate'),
        actions: [
          IconButton(
            icon: ValueListenableBuilder(
              valueListenable: _controller,
              builder: (context, state, child) {
                switch (state.torchState) {
                  case TorchState.off:
                    return const Icon(Icons.flash_off, color: Colors.grey);
                  case TorchState.on:
                    return const Icon(Icons.flash_on, color: Colors.yellow);
                  case TorchState.auto:
                    return const Icon(Icons.flash_auto, color: Colors.yellow);
                  case TorchState.unavailable:
                    return const Icon(Icons.flash_off, color: Colors.grey);
                }
              },
            ),
            onPressed: () => _controller.toggleTorch(),
          ),
          IconButton(
            icon: const Icon(Icons.cameraswitch),
            onPressed: () => _controller.switchCamera(),
          ),
        ],
      ),
      body: MobileScanner(
        controller: _controller,
        onDetect: _onDetect,
      ),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }
}
