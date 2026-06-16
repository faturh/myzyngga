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
        hasFile: false,
        toggleIssue(id) {
            if(this.issueTypes.includes(id)) {
                this.issueTypes = this.issueTypes.filter(i => i !== id);
            } else if(this.issueTypes.length < 3) {
                this.issueTypes.push(id);
            }
        }
    }">
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pengajuan Komplain" 
            :backUrl="route('order.detail', ['id' => $order['id']])" 
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                <form method="POST" action="{{ route('order.complaint.store', ['id' => $order['id']]) }}" id="complaint-form" class="flex-1 flex flex-col" enctype="multipart/form-data">
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
                                        maxlength="250"
                                        x-model="issueDesc"
                                        class="w-full h-32 p-4 border-[1.5px] border-zyngga-neutral-200 rounded-xl focus:border-zyngga-blue-300 focus:ring-0 outline-none transition-all duration-200 text-sm placeholder-zyngga-neutral-400 resize-none"
                                        placeholder="Ceritakan kendala yang kamu alami"
                                        required
                                    ></textarea>
                                    <div class="absolute bottom-3 right-4 text-xs text-zyngga-neutral-400">
                                        <span x-text="issueDesc.length">0</span>/250
                                    </div>
                                </div>
                            </div>

                            {{-- Gambar Bukti --}}
                            <div>
                                <x-zyngga-text variant="sm" weight="regular" class="mb-1.5 block">Gambar Bukti</x-zyngga-text>
                                <div class="relative w-full rounded-xl border-2 border-dashed border-zyngga-neutral-200 bg-white p-6 flex flex-col items-center justify-center text-center">
                                    <div class="w-12 h-12 bg-zyngga-neutral-50 rounded-full flex items-center justify-center mb-3">
                                        <i data-feather="image" class="w-6 h-6 text-zyngga-neutral-500"></i>
                                    </div>
                                    <x-zyngga-text variant="xs" color="neutral-500" class="mb-4">Format foto .jpg atau .png, maksimal 5MB</x-zyngga-text>
                                    
                                    <x-zyngga-button 
                                        type="button" 
                                        variant="secondary" 
                                        size="s" 
                                        label="Unggah Gambar" 
                                        icon="upload" 
                                        iconPosition="right" 
                                        onclick="document.getElementById('file-upload').click()"
                                    />
                                    <input type="file" name="issue_image" id="file-upload" class="hidden" accept=".jpg,.jpeg,.png" @change="hasFile = $event.target.files.length > 0">
                                    <div x-show="hasFile" class="mt-3 text-sm text-[#1660C1] font-medium" x-text="$refs.fileInput ? $refs.fileInput.files[0].name : 'File terpilih'" x-ref="fileNameDisplay"></div>
                                </div>
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
                            x-bind:disabled="issueTypes.length === 0 || issueDesc.length === 0 || !hasFile"
                            onclick="document.getElementById('complaint-form').submit()"
                        />
                    </div>
                </div>

            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
            
            // Sync file name display
            const fileInput = document.getElementById('file-upload');
            fileInput.addEventListener('change', function(e) {
                if(e.target.files.length > 0) {
                    document.querySelector('[x-ref="fileNameDisplay"]').textContent = e.target.files[0].name;
                }
            });
        });
    </script>
</body>
</html>
