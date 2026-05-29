        {{-- Column Visibility Toggle --}}
        <div class="flex justify-end">
            <div class="relative" id="colToggleWrap">
                <button type="button" onclick="document.getElementById('colDropdown').classList.toggle('hidden')"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    {{ __('Atur Kolom') }}
                    <span id="colCount"
                        class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full"></span>
                </button>
                <div id="colDropdown"
                    class="hidden absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-2 max-h-80 overflow-y-auto">
                    <div class="px-3 py-1.5 border-b border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-500">{{ __('Tampilkan/Sembunyikan') }}</span>
                        <button type="button" onclick="resetColumns()"
                            class="text-[10px] text-indigo-500 hover:text-indigo-700 font-semibold">Reset</button>
                    </div>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-tanggal" checked> Tanggal
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-nopol" checked> No Polisi
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-vendor" checked> Vendor
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-kendaraan" checked> Kendaraan
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-barang" checked> Barang
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-aktivitas" checked> Aktivitas
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-penerimaan" checked> Penerimaan Dok
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-start" checked> Start Loading
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-end" checked> End Loading
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-gate" checked> Gate
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-status" checked> Status
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-catatan" checked> Catatan
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-durasi" checked> Durasi
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-penyerahan" checked> Penyerahan Dok
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-durasi-dok" checked> Durasi Dokumen
                    </label>
                </div>
            </div>
        </div>
