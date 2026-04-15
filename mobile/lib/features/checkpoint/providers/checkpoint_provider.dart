import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/checkpoint_repository.dart';

final checkpointRepositoryProvider = Provider((ref) => CheckpointRepository());

final activeCheckpointsProvider = FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final repo = ref.watch(checkpointRepositoryProvider);
  return await repo.fetchActiveCheckpoints();
});
