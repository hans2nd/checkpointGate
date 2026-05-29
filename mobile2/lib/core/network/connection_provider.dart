import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:dio/dio.dart';
import '../../core/config/app_config.dart';

/// Represents the current API connection status
class ConnectionStatus {
  final bool isConnected;
  final int? pingMs;
  final String message;
  final DateTime lastChecked;

  const ConnectionStatus({
    required this.isConnected,
    this.pingMs,
    required this.message,
    required this.lastChecked,
  });

  factory ConnectionStatus.connected(int pingMs) {
    return ConnectionStatus(
      isConnected: true,
      pingMs: pingMs,
      message: 'Online - Tersambung',
      lastChecked: DateTime.now(),
    );
  }

  factory ConnectionStatus.disconnected(String reason) {
    return ConnectionStatus(
      isConnected: false,
      pingMs: null,
      message: reason,
      lastChecked: DateTime.now(),
    );
  }

  factory ConnectionStatus.checking() {
    return ConnectionStatus(
      isConnected: false,
      pingMs: null,
      message: 'Memeriksa koneksi...',
      lastChecked: DateTime.now(),
    );
  }
}

/// Provider that periodically checks API connectivity and measures ping
class ConnectionNotifier extends Notifier<ConnectionStatus> {
  Timer? _timer;

  @override
  ConnectionStatus build() {
    // Start periodic check when provider is first read
    _startPeriodicCheck();

    // Clean up timer when provider is disposed
    ref.onDispose(() {
      _timer?.cancel();
    });

    return ConnectionStatus.checking();
  }

  void _startPeriodicCheck() {
    // Initial check immediately
    Future.microtask(() => checkConnection());

    // Then check every 15 seconds
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 15), (_) {
      checkConnection();
    });
  }

  /// Manually trigger a connection check (used for pull-to-refresh)
  Future<void> checkConnection() async {
    final dio = Dio();
    dio.options.connectTimeout = const Duration(seconds: 5);
    dio.options.receiveTimeout = const Duration(seconds: 5);

    try {
      final stopwatch = Stopwatch()..start();

      // Ping the API base URL - use a lightweight endpoint
      // We strip '/api' and hit the base to check server reachability,
      // or we can hit an actual API endpoint
      final baseUrl = AppConfig.baseUrl;
      await dio.get('$baseUrl/dashboard');

      stopwatch.stop();
      final pingMs = stopwatch.elapsedMilliseconds;

      state = ConnectionStatus.connected(pingMs);
    } on DioException catch (e) {
      String reason;
      switch (e.type) {
        case DioExceptionType.connectionTimeout:
          reason = 'Timeout - Server tidak merespons';
          break;
        case DioExceptionType.receiveTimeout:
          reason = 'Timeout - Respons terlalu lama';
          break;
        case DioExceptionType.connectionError:
          reason = 'Tidak dapat terhubung ke server';
          break;
        case DioExceptionType.badResponse:
          // Server responded but with error - still "reachable"
          // 401/403 means server is up but auth issue - treat as connected
          if (e.response?.statusCode == 401 || e.response?.statusCode == 403) {
            state = ConnectionStatus.connected(0);
            return;
          }
          reason = 'Server error (${e.response?.statusCode})';
          break;
        default:
          reason = 'Koneksi terputus';
      }
      state = ConnectionStatus.disconnected(reason);
    } catch (_) {
      state = ConnectionStatus.disconnected('Koneksi terputus');
    } finally {
      dio.close();
    }
  }

  /// Restart the periodic check (useful after manual refresh)
  void restart() {
    state = ConnectionStatus.checking();
    _startPeriodicCheck();
  }
}

final connectionProvider =
    NotifierProvider<ConnectionNotifier, ConnectionStatus>(() {
  return ConnectionNotifier();
});
