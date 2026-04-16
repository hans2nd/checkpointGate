        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('checkpoints.index') }}" class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari no polisi, vendor, driver..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>
                <select name="aktivitas"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Aktivitas</option>
                    <option value="INBOUND" {{ request('aktivitas') == 'INBOUND' ? 'selected' : '' }}>Inbound</option>
                    <option value="OUTBOUND" {{ request('aktivitas') == 'OUTBOUND' ? 'selected' : '' }}>Outbound</option>
                </select>
                <select name="jenis_barang"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Barang</option>
                    <option value="FROZEN" {{ request('jenis_barang') == 'FROZEN' ? 'selected' : '' }}>Frozen</option>
                    <option value="DRY" {{ request('jenis_barang') == 'DRY' ? 'selected' : '' }}>Dry</option>
                </select>
                <select name="status"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="ON LOADING" {{ request('status') == 'ON LOADING' ? 'selected' : '' }}>On Loading</option>
                    <option value="FINISH" {{ request('status') == 'FINISH' ? 'selected' : '' }}>Finish</option>
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                <select name="per_page" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    @foreach ([10, 15, 25, 50, 100, 1000] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>
                            {{ $size }} / hal</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
                </div>
            </form>
        </div>
