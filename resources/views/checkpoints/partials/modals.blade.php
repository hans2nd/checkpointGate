<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('importModal').classList.add('hidden')">
        </div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">{{ __('Import Data Checkpoint') }}</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-700"><strong>Format kolom:</strong> Tanggal | No Polisi | Vendor |
                    Driver |
                    Tipe (INTERNAL/EKSTERNAL) | Jenis Kendaraan | Jenis Barang (FROZEN/DRY) | Aktivitas
                    (INBOUND/OUTBOUND) | Gate</p>
                <p class="text-xs text-blue-600 mt-1">{{ __('Baris pertama = header (dilewati).') }}</p>
                <a href="{{ route('checkpoints.template') }}"
                    class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-blue-700 hover:text-blue-900">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Download Template') }}
                </a>
            </div>
            <form method="POST" action="{{ route('checkpoints.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('Pilih File Excel (.xlsx)') }}</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeCancelModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Cancel Checkpoint') }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="cancelModalSubtitle"></p>
                </div>
                <button type="button" onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="cancelForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="cancel_note"
                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('Catatan Cancel') }}
                        <span class="text-red-400">*</span></label>
                    <textarea id="cancel_note" name="cancel_note" rows="4" required minlength="5" maxlength="500"
                        placeholder="{{ __('Tuliskan alasan cancel agar data tetap informatif...') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none"></textarea>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ __('Catatan ini akan tampil di tabel, detail, dan export Excel.') }}
                    </p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg">{{ __('Cancel Checkpoint') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gate Selection Modal -->
<div id="gateModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeGateModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-3xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Pilih Gate') }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="gateModalSubtitle"></p>
                </div>
                <button onclick="closeGateModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <!-- Loading state -->
            <div id="gateModalLoading" class="py-8 text-center">
                <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                    </path>
                </svg>
                <p class="text-sm text-gray-500">{{ __('Memuat gate tersedia...') }}</p>
            </div>

            <!-- Gate grid -->
            <div id="gateModalContent" class="hidden">
                <p class="text-xs text-gray-500 mb-3">🟢 Gate tersedia &nbsp; 🔴 Gate sedang loading</p>
                <div class="mb-4">
                    <p class="text-xs font-semibold text-blue-600 mb-1.5">FROZEN (Gate 1-16)</p>
                    <div class="grid grid-cols-3 gap-2.5" id="gateGridFrozen"></div>
                </div>
                <div class="mb-4">
                    <p class="text-xs font-semibold text-amber-600 mb-1.5">DRY (Gate 17-27)</p>
                    <div class="grid grid-cols-3 gap-2.5" id="gateGridDry"></div>
                </div>

                <form id="gateForm" method="POST" action="" onsubmit="handleGateSubmit(event)">
                    @csrf
                    <input type="hidden" name="gate" id="selectedGateInput" value="">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-sm text-gray-600">Gate terpilih: <span id="selectedGateLabel"
                                    class="font-bold text-orange-600">-</span></p>
                            <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                <input type="checkbox" id="printTicketCheck"
                                    class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                                <span class="text-xs text-gray-600">🖨️ Print Ticket Gate setelah terima</span>
                            </label>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="closeGateModal()"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                            <button type="submit" id="gateConfirmBtn" disabled
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">🔹
                                {{ __('Assign Gate') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Terima Modal -->
<div id="terimaModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeTerimaModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('Penerimaan Dokumen') }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="terimaModalSubtitle"></p>
                </div>
                <button type="button" onclick="closeTerimaModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="terimaForm" method="POST" action="">
                @csrf
                <div class="space-y-6 mb-12">
                    <div>
                        <label for="terima_jenis_kendaraan"
                            class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Vehicle Type') }} <span
                                class="text-red-400">*</span></label>
                        <select id="terima_jenis_kendaraan" name="jenis_kendaraan" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>{{ __('Pilih Jenis Kendaraan') }}</option>
                            @foreach($vehicleTypes as $vt)
                                <option value="{{ $vt }}">{{ $vt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="terima_tipe" class="block text-sm font-medium text-gray-700 mb-1.5">Type <span
                                class="text-red-400">*</span></label>
                        <select id="terima_tipe" name="tipe" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Pilih Tipe</option>
                            <option value="INTERNAL">INTERNAL</option>
                            <option value="EKSTERNAL">EKSTERNAL</option>
                        </select>
                    </div>
                    <div>
                        <label for="terima_jenis_barang" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis
                            Barang <span class="text-red-400">*</span></label>
                        <select id="terima_jenis_barang" name="jenis_barang" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Pilih Jenis Barang</option>
                            <option value="FROZEN">FROZEN</option>
                            <option value="DRY">DRY</option>
                            <option value="CHILLED">CHILLED</option>
                        </select>
                    </div>
                    <div>
                        <label for="terima_aktivitas" class="block text-sm font-medium text-gray-700 mb-1.5">Aktivitas
                            <span class="text-red-400">*</span></label>
                        <select id="terima_aktivitas" name="aktivitas" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Pilih Aktivitas</option>
                            <option value="INBOUND">INBOUND</option>
                            <option value="OUTBOUND">OUTBOUND</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No.
                            Surat Jalan</label>
                        <div id="suratJalanContainer" class="space-y-2">
                            <!-- Inputs will be generated here by JS -->
                        </div>
                        <input type="hidden" id="terima_no_surat_jalan" name="no_surat_jalan">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Purchase Order</label>
                        <div id="poContainer" class="space-y-2">
                            <!-- Inputs will be generated here by JS -->
                        </div>
                        <input type="hidden" id="terima_purchase_order" name="purchase_order">
                    </div>
                    <div>
                        <label for="terima_note" class="block text-sm font-medium text-gray-700 mb-1.5">Note /
                            Description</label>
                        <textarea id="terima_note" name="note" rows="5"
                            placeholder="Contoh Inputan : Full Botan / 1.500 Ctn / 2 SKU"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeTerimaModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg">Simpan
                        & Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL SERAH DOKUMEN -->
<div id="serahModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeSerahModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Penyerahan Dokumen</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="serahModalSubtitle">Detail Penyerahan</p>
                </div>
                <button type="button" onclick="closeSerahModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="serahForm" method="POST" action="">
                @csrf
                <div class="space-y-4 mb-6">
                    <!-- Info Text -->
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <span class="font-semibold text-gray-500">Kendaraan</span>
                            <span class="col-span-2 font-medium" id="serah_kendaraan">-</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <span class="font-semibold text-gray-500">Vendor</span>
                            <span class="col-span-2 font-medium" id="serah_vendor">-</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <span class="font-semibold text-gray-500">Driver</span>
                            <span class="col-span-2 font-medium" id="serah_driver">-</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <span class="font-semibold text-gray-500">Surat Jalan</span>
                            <span class="col-span-2 font-medium" id="serah_surat_jalan">-</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="font-semibold text-gray-500">PO Number</span>
                            <span class="col-span-2 font-medium" id="serah_po">-</span>
                        </div>
                    </div>

                    <!-- Receipt Number Input Container -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Receipt Number</label>
                        <div id="receiptContainer" class="space-y-2">
                            <!-- Inputs will be generated here by JS -->
                        </div>
                        <input type="hidden" id="serah_receipt_number" name="receipt_number">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeSerahModal()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm font-semibold rounded-lg">Simpan & Serah</button>
                </div>
            </form>
        </div>
    </div>
</div>