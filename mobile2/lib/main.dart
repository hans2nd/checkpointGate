import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'core/config/app_config.dart';
import 'core/theme/app_theme.dart';
import 'features/auth/presentation/login_screen.dart';
import 'features/auth/presentation/settings_screen.dart';
import 'features/auth/providers/auth_provider.dart';
import 'features/home/presentation/home_screen.dart';
import 'features/checkpoint/presentation/search_screen.dart';
import 'features/checkpoint/presentation/checkpoint_form_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await AppConfig.loadConfig();

  runApp(
    const ProviderScope(
      child: CheckpointApp(),
    ),
  );
}

// 1. Pindahkan Router ke dalam Provider agar bisa membaca auth state
final routerProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: '/splash',
    routes: [
      GoRoute(
        path: '/splash',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/settings',
        builder: (context, state) => const SettingsScreen(),
      ),
      GoRoute(
        path: '/home',
        builder: (context, state) => const HomeScreen(),
      ),
      GoRoute(
        path: '/search',
        builder: (context, state) => const SearchScreen(),
      ),
      GoRoute(
        path: '/form',
        builder: (context, state) => Scaffold(
          appBar: AppBar(title: const Text('Input Kedatangan')),
          body: const CheckpointFormScreen(),
        ),
      ),
    ],
    // 2. Gunakan redirect untuk menangani perpindahan halaman berdasarkan auth
    redirect: (context, state) {
      final authState = ref.watch(authControllerProvider);
      final currentLocation = state.matchedLocation;
      final isLoggingIn = currentLocation == '/login';
      final isSplash = currentLocation == '/splash';
      final isSettings = currentLocation == '/settings';

      // Halaman settings boleh diakses kapan saja (tanpa auth)
      if (isSettings) return null;

      if (authState == AuthState.authenticated) {
        if (isLoggingIn || isSplash) return '/home';
      } else if (authState == AuthState.unauthenticated || authState == AuthState.error) {
        if (!isLoggingIn) return '/login';
      }
      // AuthState.loading / AuthState.initial -> tetap di halaman saat ini (splash)
      return null;
    },
  );
});

// 3. Pisahkan SplashScreen agar lebih rapi
class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.local_shipping, size: 64, color: Color(0xFFF97316)),
            SizedBox(height: 16),
            CircularProgressIndicator(),
            SizedBox(height: 16),
            Text('Memeriksa koneksi...', style: TextStyle(color: Colors.grey)),
          ],
        ),
      ),
    );
  }
}

class CheckpointApp extends ConsumerWidget { // Ubah ke ConsumerWidget
  const CheckpointApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // 4. Ambil config router dari provider
    final router = ref.watch(routerProvider);

    return MaterialApp.router(
      title: 'Checkpoint GIIC',
      theme: AppTheme.lightTheme,
      routerConfig: router,
      debugShowCheckedModeBanner: false,
    );
  }
}
