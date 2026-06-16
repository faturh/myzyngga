<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Pickup – Zyngga</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
        html, body { margin: 0; background: #e8eff9; }

        /* ── scrollable main content ── */
        #page-content {
            /* padding-bottom removed to allow cleaner structure */
        }


        /* ── service option ── */
        .service-option {
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            margin-bottom: 12px;
            height: 72px;
        }
        .service-option:last-child { margin-bottom: 0; }
        .service-option.selected {
            border-color: #1660C1;
            background: #e8eff9;
            height: 72px;
        }

        /* ── date button ── */
        .date-btn {
            flex: 1;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            text-align: left;
            height: 72px;
        }
        .date-btn:hover {
            border-color: #1660C1;
            background: #e8eff9;
        }
        .date-btn.selected {
            border-color: #1660C1;
            background: #e8eff9;
        }

        /* ── time chip ── */
        .time-chip {
            flex: 1;
            height: 40px;
            border: 1.5px solid #e8eff9;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #808080;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .time-chip:not(:disabled):hover {
            border-color: #1660C1;
            color: #1660C1;
            background: #e8eff9;
        }
        .time-chip.selected {
            border-color: #1660C1;
            background: #e8eff9;
            color: #0F0F0F;
            font-weight: 600;
        }

        /* ── addon row ── */
        .addon-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 32px;
            cursor: pointer;
        }

        /* ── payment radio ── */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            cursor: pointer;
        }
        .radio-circle {
            width: 20px; height: 20px;
            border-radius: 50%;
            border: 2px solid #e8eff9;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.15s;
        }
        .radio-circle.checked {
            border-color: #1660C1;
            background: #1660C1;
        }
        .radio-circle.checked::after {
            content: '';
            width: 6px; height: 6px;
            background: white;
            border-radius: 50%;
        }

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
            justify-content: space-between;
            z-index: 50;
            box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        /* Reset sticky footer position (no sidebar) */
        /* Remove specific centering on desktop */
        @media (min-width: 768px) {
            #sticky-footer {
                left: 0;
                right: 0;
                transform: none;
            }
        }

        /* ── map thumbnail ── */
        #map-thumb {
            width: 100%;
            height: 144px;
            border-radius: 8px;
            overflow: hidden;
        }
        #map-thumb iframe { width:100%; height:100%; border:0; }
    </style>
</head>
<body class="bg-[#e8eff9]">

    <div class="min-h-screen flex flex-col" x-data>
        {{-- ── HEADER ─────────────────────────────────────────────── --}}
        <x-dashboard-header 
            title="Pemesanan Pickup" 
            :backUrl="route('home')" 
            :backAction="'if(isDirty) { window.dispatchEvent(new CustomEvent(\'open-back-modal\')); return false; }'"
            :maxWidth="'max-w-full'"
            :showPoints="false"
            :back="true"
            :hamburg="false"
        />

        {{-- ── MAIN CONTENT ────────────────────────────────────────── --}}
        <main class="flex-1 flex flex-col relative">
            <div class="w-full max-w-5xl mx-auto px-5 pb-[88px]">
                {{-- ── SCROLLABLE CONTENT ──────────────────────────────────── --}}
                <form method="POST" action="{{ route('order.confirm') }}" id="page-content" class="flex-1 flex flex-col">
        @csrf
        @if ($errors->any())
            <div x-init="$dispatch('toast', { message: '{{ $errors->first() }}', type: 'error' })"></div>
        @endif
        <input type="hidden" name="service"        value="{{ $service }}">
        <input type="hidden" name="address"        value="{{ $address }}">
        <input type="hidden" name="lat"            value="{{ $lat }}">
        <input type="hidden" name="lng"            value="{{ $lng }}">
        <input type="hidden" name="selected_service_id" id="selected_service_id" value="{{ strtolower($serviceLabel) }}">
        @php
            $todayCarbon = \Carbon\Carbon::now('Asia/Jakarta');
            $currentHour = $todayCarbon->hour;
            $isTodayDisabled = $currentHour >= 17;
            $defaultDate = $isTodayDisabled ? 'tomorrow' : 'today';
        @endphp
        <input type="hidden" name="pickup_date"   id="pickup_date"   value="{{ $pickupDate ?: $defaultDate }}">
        <input type="hidden" name="pickup_time"   id="pickup_time"   value="{{ $pickupTime ?: 'Standard' }}">
        <input type="hidden" name="parfum"        id="parfum"        value="{{ $parfum ?: 'Lavender' }}">
        <input type="hidden" name="catatan"       id="note"          value="{{ $note }}">
        
        {{-- ── LOKASI PICKUP ─────────────────────────────────── --}}
        <x-zyngga-card title="Lokasi Pickup">
            <x-slot:headerAction>
