import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';

class LocalDatabase {
  static final LocalDatabase instance = LocalDatabase._init();
  static Database? _database;

  LocalDatabase._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('checkpoint_giic.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, filePath);

    return await openDatabase(
      path,
      version: 1,
      onCreate: _createDB,
    );
  }

  Future _createDB(Database db, int version) async {
    const idType = 'INTEGER PRIMARY KEY AUTOINCREMENT';
    const textType = 'TEXT NOT NULL';
    const textNullable = 'TEXT';

    await db.execute('''
      CREATE TABLE offline_queue (
        id $idType,
        endpoint $textType,
        method $textType,
        payload $textNullable,
        created_at $textType,
        status $textType
      )
    ''');
  }

  Future<int> insertQueue(Map<String, dynamic> item) async {
    final db = await instance.database;
    return await db.insert('offline_queue', item);
  }

  Future<List<Map<String, dynamic>>> getQueue() async {
    final db = await instance.database;
    return await db.query('offline_queue', where: 'status = ?', whereArgs: ['pending'], orderBy: 'created_at ASC');
  }

  Future<int> updateQueueStatus(int id, String status) async {
    final db = await instance.database;
    return await db.update(
      'offline_queue',
      {'status': status},
      where: 'id = ?',
      whereArgs: [id],
    );
  }

  Future<int> deleteQueue(int id) async {
    final db = await instance.database;
    return await db.delete(
      'offline_queue',
      where: 'id = ?',
      whereArgs: [id],
    );
  }

  Future close() async {
    final db = await instance.database;
    db.close();
  }
}
