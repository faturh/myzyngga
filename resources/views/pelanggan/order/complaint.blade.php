<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengajuan Komplain – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

        /* ── sticky footer ── */
        #sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: white;
            border-top: 1px solid #F4F4F4;
            border-radius: 16px 16px 0 0;
            padding: 16px 20px calc(16px + env(safe-area-inset-bottom, 0px));
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        @media (min-width: 768px) {
            #sticky-footer {
                left: 0;
                right: 0;
                transform: none;
            }
        }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data="{ 
        issueTypes: [], 
        issueDesc: '', 
        files: [],
        isSubmitting: false,
        complaintRedirectUrl: '',
        toggleIssue(id) {
            if(this.issueTypes.includes(id)) {
                this.issueTypes = this.issueTypes.filter(i => i !== id);
            } else if(this.issueTypes.length < 3) {
                this.issueTypes.push(id);
            }
        },
        handleFile(event) {
            const fileList = event.target.files;
            const spaceLeft = 3 - this.files.length;
            if (spaceLeft <= 0) return;
            
            const filesToProcess = Array.from(fileList).slice(0, spaceLeft);
            
            filesToProcess.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.files.push({
                        file: file,
                        name: file.name,
                        size: (file.size / (1024 * 1024)).toFixed(1) + ' MB',
                        preview: e.target.result
                    });
                    this.updateFileInput();
                    setTimeout(() => { if (window.feather) feather.replace(); }, 50);
                };
                reader.readAsDataURL(file);
            });
            
            event.target.value = '';
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.updateFileInput();
        },
        updateFileInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            document.getElementById('hidden-file-input').files = dt.files;
        },
        async submitComplaint() {
            if (this.issueTypes.length === 0 || this.issueDesc.length === 0 || this.files.length === 0 || this.isSubmitting) return;
            
            this.isSubmitting = true;
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('issue_description', this.issueDesc);
            
            this.issueTypes.forEach(type => {
                formData.append('issue_types[]', type);
            });
            
            this.files.forEach(f => {
                formData.append('issue_image[]', f.file);
            });
            
            try {
                const response = await fetch('{{ route('order.complaint.store', ['id' => $order['nota_layanan']], false) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    const errText = await response.text();
                    console.error('Error Response:', errText);
                    alert('Gagal mengirim komplain (HTTP ' + response.status + '): ' + errText.substring(0, 200));
                    return;
                }
                
                const result = await response.json();
                if (result.success) {
                    this.complaintRedirectUrl = result.redirect_url;
                    window.dispatchEvent(new CustomEvent('open-complaint-success-modal'));
                } else {
                    alert(result.message || 'Gagal mengirim komplain');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengirim komplain: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pengajuan Komplain" 
            :backUrl="route('order.detail', ['id' => $order['nota_layanan']])" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                <form method="POST" action="{{ route('order.complaint.store', ['id' => $order['nota_layanan']]) }}" id="complaint-form" class="flex-1 flex flex-col" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- ── MASALAH YANG TERJADI ──────────────────────────────── --}}
                    <x-zyngga-card title="Masalah yang Terjadi">
                        @php
                            $issues = [
                                ['id' => 'pakaian_rusak', 'label' => 'Pakaian Rusak atau Hilang'],
                                ['id' => 'masalah_pengantaran', 'label' => 'Masalah Pengantaran / Penjemputan'],
                                ['id' => 'status_pesanan', 'label' => 'Status Pesanan Tidak Sesuai'],
                                ['id' => 'kendala_pembayaran', 'label' => 'Kendala Pembayaran atau Aplikasi'],
                                ['id' => 'lainnya', 'label' => 'Lainnya'],
                            ];
                        @endphp
                        <div class="flex flex-col">
                            @foreach($issues as $i => $issue)
                                <div class="flex items-center gap-3 py-3.5" 
                                     :class="!issueTypes.includes('{{ $issue['id'] }}') && issueTypes.length >= 3 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
                                     @click="!(!issueTypes.includes('{{ $issue['id'] }}') && issueTypes.length >= 3) && toggleIssue('{{ $issue['id'] }}')">
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center shrink-0 transition-colors"
                                         :class="issueTypes.includes('{{ $issue['id'] }}') ? 'border-[#1660C1] bg-[#1660C1]' : 'border-[#e8eff9]'">
                                        <i data-feather="check" class="w-3.5 h-3.5 text-white" x-show="issueTypes.includes('{{ $issue['id'] }}')"></i>
                                    </div>
                                    <x-zyngga-text variant="sm" weight="medium" class="m-0 text-zyngga-neutral-900">{{ $issue['label'] }}</x-zyngga-text>
                                    <input type="checkbox" name="issue_types[]" value="{{ $issue['id'] }}" class="hidden" x-model="issueTypes">
                                </div>
                                @if ($i < count($issues) - 1)
                                    <x-zyngga-divider class="my-0" />
                                @endif
                            @endforeach
                        </div>
                    </x-zyngga-card>

                    {{-- ── DETAIL MASALAH ────────────────────────────────────── --}}
                    <x-zyngga-card title="Detail Masalah">
                        <div class="space-y-4">
                            {{-- Deskripsi Masalah --}}
                            <div>
                                <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Deskripsi Masalah</x-zyngga-text>
                                <div class="relative">
                                    <textarea 
                                        name="issue_description"
                                        id="issue_description"
                                        maxlength="180"
                                        x-model="issueDesc"
                                        class="w-full h-32 p-4 border-[1.5px] border-zyngga-neutral-200 rounded-xl focus:border-zyngga-blue-300 focus:ring-0 outline-none transition-all duration-200 text-sm placeholder-zyngga-neutral-400 resize-none"
                                        placeholder="Ceritakan kendala yang kamu alami"
                                        required
                                    ></textarea>
                                    <div class="absolute bottom-3 right-4 text-xs text-zyngga-neutral-400">
                                        <span x-text="issueDesc.length">0</span>/180
                                    </div>
                                </div>
                            </div>

                            {{-- Gambar Bukti --}}
                            <div>
                                <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Gambar Bukti</x-zyngga-text>
                                <div class="w-full flex flex-col gap-3">
                                    <div x-cloak x-show="files.length > 0" class="w-full space-y-3">
                                        <template x-for="(f, index) in files" :key="index">
                                            <div class="w-full bg-[#F4F4F4] rounded-xl p-3 flex items-center gap-4">
                                                <div class="w-[60px] h-[60px] shrink-0 rounded-lg overflow-hidden bg-white">
                                                    <img :src="f.preview" class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1 text-left flex flex-col min-w-0">
                                                    <x-zyngga-text variant="sm" class="text-neutral-900 truncate" x-text="f.name"></x-zyngga-text>
                                                    <x-zyngga-text variant="xs" color="neutral-500" x-text="f.size"></x-zyngga-text>
                                                </div>
                                                <button type="button" @click="removeFile(index)" class="p-2 shrink-0 text-zyngga-status-danger hover:bg-red-50 rounded-lg transition-colors">
                                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="files.length < 3" class="relative w-full rounded-xl border-2 border-dashed border-zyngga-neutral-200 bg-white p-4 flex flex-col items-center justify-center text-center">
                                        <div x-show="files.length === 0">
                                            <div class="w-12 h-12 bg-[#F4F4F4] rounded-full flex items-center justify-center mx-auto mb-4">
                                                <img src="{{ asset('assets/images/image.svg') }}" alt="Icon Gambar" width="24" height="24">
                                            </div>
                                            <x-zyngga-text variant="xs" color="neutral-500" class="mb-4 block">Format foto .jpg atau .png, maksimal 5MB</x-zyngga-text>
                                        </div>
                                        
                                        <x-zyngga-button 
                                            type="button" 
                                            variant="secondary" 
                                            size="s" 
                                            label="Unggah Gambar" 
                                            icon="upload" 
                                            iconPosition="right" 
                                            onclick="document.getElementById('file-upload').click()"
                                        />
                                    </div>
                                </div>
                                
                                <input type="file" name="issue_image[]" id="hidden-file-input" class="hidden" multiple accept=".jpg,.jpeg,.png">
                                <input type="file" id="file-upload" class="hidden" accept=".jpg,.jpeg,.png" multiple @change="handleFile($event)">
                            </div>
                        </div>
                    </x-zyngga-card>

                </form>

                {{-- ── STICKY FOOTER ──────────────────────────────────────── --}}
                <div id="sticky-footer">
                    <div class="max-w-5xl mx-auto w-full px-5">
                            <x-zyngga-button 
                                type="button"
                                variant="primary"
                                size="l"
                                label="Kirim Komplain"
                                class="w-full"
                                x-bind:disabled="issueTypes.length === 0 || issueDesc.length === 0 || files.length === 0 || isSubmitting"
                                @click="submitComplaint()"
                            />
                    </div>
                </div>

            </div>
        </main>

        {{-- ── MODAL: COMPLAINT SUCCESS ───────────────────────────── --}}
        <x-zyngga-selection-modal 
            id="complaint-success-modal" 
            openEvent="open-complaint-success-modal"
            closeEvent="close-complaint-success-modal"
        >
            <x-zyngga-confirm-view
                :image="asset('images/illustrations/confirm_order.png')"
                title="Pengajuan Komplain Berhasil"
                description="Laporan komplain kamu telah kami terima. Mohon tunggu kami memeriksa kendala tersebut."
                primaryLabel="Lihat Detail"
                secondaryLabel="Kembali"
                primaryAction="window.location.href = complaintRedirectUrl"
                secondaryAction="window.location.href = '{{ route('order.detail', ['id' => $order['nota_layanan']]) }}'"
            />
        </x-zyngga-selection-modal>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
