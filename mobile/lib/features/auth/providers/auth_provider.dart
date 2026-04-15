import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/auth_repository.dart';

enum AuthState { initial, loading, authenticated, unauthenticated, error }

final authRepositoryProvider = Provider((ref) => AuthRepository());

final authControllerProvider = NotifierProvider<AuthController, AuthState>(() {
  return AuthController();
});

class AuthController extends Notifier<AuthState> {
  String _errorMessage = '';

  @override
  AuthState build() {
    Future.microtask(() => checkToken());
    return AuthState.initial;
  }

  String get errorMessage => _errorMessage;

  Future<void> checkToken() async {
    state = AuthState.loading;
    try {
      final isValid = await ref.read(authRepositoryProvider).checkAuth();
      if (isValid) {
        state = AuthState.authenticated;
      } else {
        state = AuthState.unauthenticated;
      }
    } catch (e) {
      // Jika terjadi error (timeout, koneksi gagal, dll),
      // langsung arahkan ke login, jangan stuck di splash
      _errorMessage = e.toString();
      state = AuthState.unauthenticated;
    }
  }

  Future<bool> login(String email, String password) async {
    state = AuthState.loading;
    try {
      await ref.read(authRepositoryProvider).login(email, password);
      state = AuthState.authenticated;
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      state = AuthState.error;
      return false;
    }
  }

  Future<void> logout() async {
    state = AuthState.loading;
    await ref.read(authRepositoryProvider).logout();
    state = AuthState.unauthenticated;
  }
}
