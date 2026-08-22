        <div class="checkpoint-filter-card mb-4">
            <div class="filter-header">
                <div class="filter-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </div>
                <h3>{{ __('Filter') }} &amp; {{ __('Pencarian Checkpoint') }}</h3>
            </div>
            <form method="GET" action="{{ route('checkpoints.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label>{{ __('Search keywords...') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Cari no polisi, vendor, driver...') }}">
                </div>
                <div>
                    <label>{{ __('Tanggal') }}</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}">
                </div>
                <div>
                    <label>{{ __('Aktivitas') }}</label>
                    <select name="aktivitas">
                        <option value="">{{ __('Semua Aktivitas') }}</option>
                        <option value="INBOUND" {{ request('aktivitas') == 'INBOUND' ? 'selected' : '' }}>Inbound</option>
                        <option value="OUTBOUND" {{ request('aktivitas') == 'OUTBOUND' ? 'selected' : '' }}>Outbound</option>
                    </select>
                </div>
                <div>
                    <label>{{ __('Jenis Barang') }}</label>
                    <select name="jenis_barang">
                        <option value="">{{ __('Semua Barang') }}</option>
                        <option value="FROZEN" {{ request('jenis_barang') == 'FROZEN' ? 'selected' : '' }}>Frozen</option>
                        <option value="DRY" {{ request('jenis_barang') == 'DRY' ? 'selected' : '' }}>Dry</option>
                        <option value="CHILLED" {{ request('jenis_barang') == 'CHILLED' ? 'selected' : '' }}>Chilled</option>
                    </select>
                </div>
                <div>
                    <label>{{ __('Status') }}</label>
                    <select name="status">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="START" {{ request('status') == 'START' ? 'selected' : '' }}>Start</option>
                        <option value="ON LOADING" {{ request('status') == 'ON LOADING' ? 'selected' : '' }}>On Loading</option>
                        <option value="FINISH" {{ request('status') == 'FINISH' ? 'selected' : '' }}>Finish</option>
                        <option value="CANCEL" {{ request('status') == 'CANCEL' ? 'selected' : '' }}>Cancel</option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <label>{{ __('Per Halaman') }}</label>
                    <select name="per_page">
                        @foreach ([10, 15, 25, 50, 100, 1000] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>
                                {{ $size }} {{ __('/ hal') }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-actions lg:col-span-6 flex gap-2">
                    <button type="submit" class="btn-filter" style="width: auto; padding-left: 24px; padding-right: 24px;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('checkpoints.index') }}" class="btn-reset" title="{{ __('Reset') }}" style="width: 40px; padding: 0;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>
