<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Runner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-terminal mr-3"></i> Artisan Command Runner
                </h2>
            </div>
            
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm">
                    Pilih command artisan di bawah ini untuk dieksekusi secara langsung.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <button onclick="runCommand('migrate')" class="flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-database mr-2 w-5"></i> php artisan migrate
                    </button>
                    
                    <button onclick="runCommand('migrate:fresh')" class="flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-exclamation-triangle mr-2 w-5"></i> php artisan migrate:fresh
                    </button>
                    
                    <button onclick="runCommand('db:seed')" class="flex items-center justify-center bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-seedling mr-2 w-5"></i> php artisan db:seed
                    </button>
                    
                    <button onclick="runCommand('storage:link')" class="flex items-center justify-center bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-link mr-2 w-5"></i> php artisan storage:link
                    </button>
                    
                    <button onclick="runCommand('cache:clear')" class="flex items-center justify-center bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border border-yellow-200 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-broom mr-2 w-5"></i> php artisan cache:clear
                    </button>

                    <button onclick="runCommand('optimize:clear')" class="flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-700 border border-gray-300 font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-bolt mr-2 w-5"></i> php artisan optimize:clear
                    </button>
                </div>

                <div class="relative bg-gray-900 rounded-lg p-4 shadow-inner">
                    <div class="flex items-center justify-between mb-3 border-b border-gray-700 pb-2">
                        <h3 class="text-gray-300 font-semibold text-sm flex items-center">
                            <i class="fas fa-code mr-2"></i> Console Output
                        </h3>
                        <button onclick="clearConsole()" class="text-xs text-gray-400 hover:text-white transition">
                            <i class="fas fa-eraser"></i> Clear
                        </button>
                    </div>
                    <pre id="console-output" class="text-green-400 font-mono text-sm whitespace-pre-wrap min-h-[250px] max-h-[500px] overflow-y-auto">Ready...</pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clearConsole() {
            document.getElementById('console-output').innerText = 'Ready...';
        }

        function runCommand(command) {
            if (command === 'migrate:fresh' && !confirm('WARNING: `migrate:fresh` akan MENGHAPUS (DROP) SEMUA TABEL di database! Apakah Anda yakin ingin melanjutkan?')) {
                return;
            }

            const outputEl = document.getElementById('console-output');
            outputEl.innerText = '> php artisan ' + command + '\nMenjalankan perintah, mohon tunggu...\n';

            fetch('{{ url("/artisan-run") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ command: command })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    outputEl.innerText = "> php artisan " + command + "\n\n" + (data.output || 'Berhasil tanpa output log.');
                } else {
                    outputEl.innerText = "> php artisan " + command + "\n\nERROR: \n" + (data.message || 'Unknown error');
                }
            })
            .catch(error => {
                outputEl.innerText += "\nFetch Error: " + error;
            });
        }
    </script>
</body>
</html>
