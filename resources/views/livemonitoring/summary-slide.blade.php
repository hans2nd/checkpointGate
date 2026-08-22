<div class="slide-1-content" id="slide-1-content">

    {{-- Activity Summary --}}
    <div id="activity-card">
        <div class="inner">
            <p style="font-size:1em;font-weight:700;color:#1f2937;margin-bottom:0.5em">Activity Summary</p>
            <p style="font-size:0.75em;color:#9ca3af;margin-bottom:1em">Periode: {{ now()->format('d/m/Y') }}</p>
            <table>
                <thead>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <th class="lbl" style="text-align:left;width:120px">Activity</th>
                        <th class="lbl" style="text-align:center; background-color:#f3f4f6; color:#4b5563; padding: 4px 8px; border-radius: 4px 0 0 0;">Parking</th>
                        <th class="lbl" style="text-align:center; background-color:#eff6ff; color:#2563eb; padding: 4px 8px;">Doc In</th>
                        <th class="lbl" style="text-align:center; background-color:#eef2ff; color:#4f46e5; padding: 4px 8px;">Waiting Gate</th>
                        <th class="lbl" style="text-align:center; background-color:#faf5ff; color:#9333ea; padding: 4px 8px;">Loading/Unloading</th>
                        <th class="lbl" style="text-align:center; background-color:#fff1f2; color:#e11d48; padding: 4px 8px;">Finish <span style="font-size:0.75em;font-weight:normal;opacity:0.8">(Today)</span></th>
                        <th class="lbl" style="text-align:center; background-color:#f0fdfa; color:#0d9488; padding: 4px 8px;">Doc Out <span style="font-size:0.75em;font-weight:normal;opacity:0.8">(Today)</span></th>
                        <th class="lbl" style="text-align:center; background-color:#ecfdf5; color:#059669; padding: 4px 8px; border-radius: 0 4px 0 0;">Completed <span style="font-size:0.75em;font-weight:normal;opacity:0.8">(Today)</span></th>
                    </tr>
                </thead>
                <tbody id="activity-summary-body">
                    @foreach ($activitySummary as $row)
                        @php
                            $isTotal = $row['label'] === 'TOTAL';
                            $rowStyle = $isTotal ? 'border-top:2px solid #e5e7eb; background:#f9fafb;' : 'border-top:1px solid #f9fafb;';
                            $labelStyle = $isTotal ? 'font-weight:800;color:#111827;text-transform:uppercase' : 'font-weight:600;color:#1f2937';
                            $numStyle = $isTotal ? 'font-weight:700;' : '';

                            $getPill = function($val, $color) use ($isTotal, $numStyle) {
                                if ($val == 0) return "<span class=\"stat-num text-gray-300\" style=\"{$numStyle}\">0</span>";
                                
                                $classes = "";
                                if ($color === 'gray') $classes = $isTotal ? "bg-gray-200 text-gray-800" : "bg-gray-100 text-gray-700";
                                if ($color === 'blue') $classes = $isTotal ? "bg-blue-200 text-blue-800" : "bg-blue-100 text-blue-700";
                                if ($color === 'indigo') $classes = $isTotal ? "bg-indigo-200 text-indigo-800" : "bg-indigo-100 text-indigo-700";
                                if ($color === 'purple') $classes = $isTotal ? "bg-purple-200 text-purple-800" : "bg-purple-100 text-purple-700";
                                if ($color === 'rose') $classes = $isTotal ? "bg-rose-200 text-rose-800" : "bg-rose-100 text-rose-700";
                                if ($color === 'teal') $classes = $isTotal ? "bg-teal-200 text-teal-800" : "bg-teal-100 text-teal-700";
                                if ($color === 'emerald') $classes = $isTotal ? "bg-emerald-200 text-emerald-800" : "bg-emerald-100 text-emerald-700";
                                
                                return "<span class=\"stat-num px-2 py-0.5 rounded font-semibold {$classes}\" style=\"{$numStyle}\">{$val}</span>";
                            };
                        @endphp
                        <tr style="{{ $rowStyle }}">
                            <td class="lbl" style="{{ $labelStyle }}">{{ $row['label'] }}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['parking'], 'gray') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['doc_in'], 'blue') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['waiting_gate'], 'indigo') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['on_process'], 'purple') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['finish'], 'rose') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['doc_out'], 'teal') !!}</td>
                            <td style="text-align:center; padding: 6px 0;">{!! $getPill($row['completed'], 'emerald') !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Average Loading Time --}}
    <div id="avg-card">
        <div class="inner">
            <p class="card-title">Average Loading Time</p>
            <div style="flex: 1; min-height: 0; overflow: auto; width: 100%;">
                <table style="border-collapse:separate; border-spacing:0; width: 100%; min-width: 800px;">
                    <thead>
                        <tr>
                            <th rowspan="3" style="position: sticky; left: 0; background: #fff; z-index: 10; text-align:left;font-weight:600;color:#6b7280;padding:2px 8px 2px 0;border-right:1px solid #e5e7eb;white-space:nowrap;vertical-align:bottom;min-width:80px;font-size:1.5em">Kendaraan</th>
                            <th colspan="4" style="text-align:center;font-weight:600;color:#15803d;background:#f0fdf4;border:1px solid #e5e7eb;padding:3px 4px">DRY</th>
                            <th colspan="4" style="text-align:center;font-weight:600;color:#1d4ed8;background:#eff6ff;border:1px solid #e5e7eb;padding:3px 4px">FROZEN</th>
                            <th colspan="4" style="text-align:center;font-weight:600;color:#b45309;background:#fffbeb;border:1px solid #e5e7eb;padding:3px 4px">CHILLED</th>
                        </tr>
                        <tr>
                            @foreach (['DRY', 'DRY', 'FROZEN', 'FROZEN', 'CHILLED', 'CHILLED'] as $gi => $g)
                                @php $sub = $gi % 2 === 0 ? 'ALT' : 'AUT'; @endphp
                                <th colspan="2" style="text-align:center;padding:2px 4px;border:1px solid #e5e7eb;white-space:nowrap;{{ $g === 'DRY' ? 'color:#16a34a;background:#f0fdf4' : ($g === 'FROZEN' ? 'color:#2563eb;background:#eff6ff' : 'color:#d97706;background:#fffbeb') }}">
                                    {{ $sub }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach (['text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50'] as $idx => $cls)
                                <th style="text-align:center;padding:2px 6px;border:1px solid #e5e7eb">{{ $idx % 2 === 0 ? 'Today' : 'L.Month' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="avg-times-body">
                        @foreach ($avgTimes as $row)
                            @php
                                $gc = function ($t, $lm) {
                                    if (!$t) {
                                        return 'color:#d1d5db';
                                    }
                                    if (!$lm) {
                                        return 'color:#374151';
                                    }
                                    if ($t > $lm) {
                                        return 'color:#dc2626';
                                    }
                                    if ($t < $lm) {
                                        return 'color:#16a34a';
                                    }
                                    return 'color:#374151';
                                };
                            @endphp
                            <tr style="border-top:1px solid #f9fafb">
                                <td style="position: sticky; left: 0; background: #fff; z-index: 5; padding:3px 8px 3px 0;font-weight:500;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap;font-size:0.90em">{{ $row['jenis_kendaraan'] }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_ALT'] ?? null, $row['DRY_ALT_LMONTH'] ?? null) }}">{{ $row['DRY_ALT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['DRY_ALT_LMONTH'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_AUT'] ?? null, $row['DRY_AUT_LMONTH'] ?? null) }}">{{ $row['DRY_AUT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['DRY_AUT_LMONTH'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_ALT'] ?? null, $row['FROZEN_ALT_LMONTH'] ?? null) }}">{{ $row['FROZEN_ALT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['FROZEN_ALT_LMONTH'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_AUT'] ?? null, $row['FROZEN_AUT_LMONTH'] ?? null) }}">{{ $row['FROZEN_AUT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['FROZEN_AUT_LMONTH'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_ALT'] ?? null, $row['CHILLED_ALT_LMONTH'] ?? null) }}">{{ $row['CHILLED_ALT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['CHILLED_ALT_LMONTH'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_AUT'] ?? null, $row['CHILLED_AUT_LMONTH'] ?? null) }}">{{ $row['CHILLED_AUT'] ?? '—' }}</td>
                                <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['CHILLED_AUT_LMONTH'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
