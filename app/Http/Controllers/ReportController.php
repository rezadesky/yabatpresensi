<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Institution;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periodType = $request->get('period_type', 'bulanan'); // bulanan, tahunan, rentang
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $institutionId = $request->get('institution_id');

        $institutions = Institution::where('is_active', true)->get();
        $selectedInstitution = $institutionId ? Institution::find($institutionId) : null;

        // Label Periode
        if ($periodType === 'bulanan') {
            $periodLabel = Carbon::create($selectedYear, $selectedMonth, 1)->locale('id')->isoFormat('MMMM Y');
        } elseif ($periodType === 'tahunan') {
            $periodLabel = "Tahun " . $selectedYear;
        } else {
            $periodLabel = Carbon::parse($startDate)->isoFormat('D MMM Y') . ' - ' . Carbon::parse($endDate)->isoFormat('D MMM Y');
        }

        // Query Dasar Kehadiran Sesuai Periode
        $baseQuery = Attendance::with(['employee', 'institution']);

        if ($periodType === 'bulanan') {
            $baseQuery->whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        } elseif ($periodType === 'tahunan') {
            $baseQuery->whereYear('date', $selectedYear);
        } else {
            $baseQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // Semua rekaman kehadiran dalam periode (untuk ringkasan per institusi)
        $allPeriodAttendances = (clone $baseQuery)->get();

        // Rekapitulasi per unit untuk kartu pilihan institusi
        $unitSummary = $institutions->map(function ($inst) use ($allPeriodAttendances) {
            $instAttendances = $allPeriodAttendances->where('institution_id', $inst->id);
            $instTotal = $instAttendances->count();
            $instHadir = $instAttendances->where('status', 'hadir')->count();
            $instTerlambat = $instAttendances->where('status', 'terlambat')->count();
            $instIzin = $instAttendances->whereIn('status', ['izin', 'sakit'])->count();
            $rate = $instTotal > 0 ? round(($instHadir / $instTotal) * 100, 1) : 100.0;

            return [
                'institution' => $inst,
                'total_employees' => $inst->employees()->count(),
                'total_attendances' => $instTotal,
                'hadir' => $instHadir,
                'terlambat' => $instTerlambat,
                'izin' => $instIzin,
                'rate' => $rate,
            ];
        });

        // Rekap agregat institusi terpilih
        $totalRecords = 0;
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $disciplineRate = 100.0;
        $monthlyBreakdown = [];

        if ($selectedInstitution) {
            $instAttendances = $allPeriodAttendances->where('institution_id', $selectedInstitution->id);

            $totalRecords = $instAttendances->count();
            $totalHadir = $instAttendances->where('status', 'hadir')->count();
            $totalTerlambat = $instAttendances->where('status', 'terlambat')->count();
            $totalIzin = $instAttendances->whereIn('status', ['izin', 'sakit'])->count();

            $disciplineRate = $totalRecords > 0 
                ? round(($totalHadir / $totalRecords) * 100, 1) 
                : 100.0;

            // Jika periode tahunan, buat rincian 12 bulan
            if ($periodType === 'tahunan') {
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = Carbon::create($selectedYear, $m, 1)->locale('id')->isoFormat('MMMM');
                    $monthAtt = $instAttendances->filter(function ($item) use ($m) {
                        return Carbon::parse($item->date)->month == $m;
                    });
                    $mTotal = $monthAtt->count();
                    $mHadir = $monthAtt->where('status', 'hadir')->count();
                    $monthlyBreakdown[] = [
                        'month_number' => $m,
                        'month_name' => $monthName,
                        'total' => $mTotal,
                        'hadir' => $mHadir,
                        'terlambat' => $monthAtt->where('status', 'terlambat')->count(),
                        'izin' => $monthAtt->whereIn('status', ['izin', 'sakit'])->count(),
                        'rate' => $mTotal > 0 ? round(($mHadir / $mTotal) * 100, 1) : 100.0,
                    ];
                }
            }
        }

        return view('admin.laporan', compact(
            'periodType',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'institutionId',
            'selectedInstitution',
            'institutions',
            'periodLabel',
            'unitSummary',
            'totalRecords',
            'totalHadir',
            'totalTerlambat',
            'totalIzin',
            'disciplineRate',
            'monthlyBreakdown'
        ));
    }

    public function exportExcel(Request $request)
    {
        $institutionId = $request->get('institution_id');
        $periodType = $request->get('period_type', 'bulanan');
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $institution = Institution::findOrFail($institutionId);

        if ($periodType === 'bulanan') {
            $periodLabel = Carbon::create($selectedYear, $selectedMonth, 1)->locale('id')->isoFormat('MMMM Y');
        } elseif ($periodType === 'tahunan') {
            $periodLabel = "Tahun " . $selectedYear;
        } else {
            $periodLabel = Carbon::parse($startDate)->isoFormat('D MMM Y') . ' - ' . Carbon::parse($endDate)->isoFormat('D MMM Y');
        }

        $query = Attendance::with(['employee'])
            ->where('institution_id', $institution->id);

        if ($periodType === 'bulanan') {
            $query->whereYear('date', $selectedYear)->whereMonth('date', $selectedMonth);
        } elseif ($periodType === 'tahunan') {
            $query->whereYear('date', $selectedYear);
        } else {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $totalRecords = $attendances->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalTerlambat = $attendances->where('status', 'terlambat')->count();
        $totalIzin = $attendances->whereIn('status', ['izin', 'sakit'])->count();
        $disciplineRate = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 100.0;

        $fileName = 'Rekap_Presensi_' . str_replace(' ', '_', $institution->name) . '_' . date('Ymd_His') . '.xls';

        $latePct = $totalRecords > 0 ? round(($totalTerlambat / $totalRecords) * 100, 1) : 0;
        $leavePct = $totalRecords > 0 ? round(($totalIzin / $totalRecords) * 100, 1) : 0;

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Rekap Presensi</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                body { font-family: Calibri, Arial, sans-serif; }
                .title { font-size: 16pt; font-weight: bold; color: #1e3a8a; }
                .subtitle { font-size: 11pt; font-weight: bold; color: #475569; }
                .meta-label { font-weight: bold; color: #334155; }
                .tbl-header { background-color: #2563eb; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #1d4ed8; }
                .tbl-summary-header { background-color: #f1f5f9; color: #1e293b; font-weight: bold; border: 1px solid #cbd5e1; }
                .tbl-cell { border: 1px solid #cbd5e1; padding: 6px; font-size: 10pt; }
                .tbl-cell-center { border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-size: 10pt; }
                .tbl-cell-right { border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-size: 10pt; }
                .status-hadir { color: #15803d; font-weight: bold; }
                .status-terlambat { color: #b45309; font-weight: bold; }
                .status-izin { color: #1d4ed8; font-weight: bold; }
                .status-alpa { color: #b91c1c; font-weight: bold; }
            </style>
        </head>
        <body>
            <table border="0">
                <tr><td colspan="9" class="title">LEMBAR REKAPITULASI PRESENSI PEGAWAI</td></tr>
                <tr><td colspan="9" class="subtitle">YAYASAN ANAK BANGSA ACEH TENGGARA</td></tr>
                <tr><td colspan="9" style="height: 10px;"></td></tr>
                <tr>
                    <td colspan="2" class="meta-label">Unit Institusi</td>
                    <td colspan="7">: ' . htmlspecialchars($institution->name) . ' (' . htmlspecialchars($institution->category) . ')</td>
                </tr>
                <tr>
                    <td colspan="2" class="meta-label">Alamat Kampus</td>
                    <td colspan="7">: ' . htmlspecialchars($institution->address) . '</td>
                </tr>
                <tr>
                    <td colspan="2" class="meta-label">Periode Laporan</td>
                    <td colspan="7">: ' . htmlspecialchars($periodLabel) . '</td>
                </tr>
                <tr>
                    <td colspan="2" class="meta-label">Waktu Unduh Data</td>
                    <td colspan="7">: ' . date('d F Y, H:i') . ' WIB</td>
                </tr>
                <tr><td colspan="9" style="height: 15px;"></td></tr>
            </table>

            <!-- Tabel Ringkasan Eksekutif -->
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th colspan="3" class="tbl-summary-header" style="text-align: left; font-size: 11pt;">RINGKASAN PARAMETER KEHADIRAN</th>
                    </tr>
                    <tr class="tbl-summary-header">
                        <th style="width: 280px; text-align: left;">PARAMETER</th>
                        <th style="width: 120px; text-align: center;">JUMLAH</th>
                        <th style="width: 120px; text-align: center;">PERSENTASE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tbl-cell">Total Catatan Presensi Masuk</td>
                        <td class="tbl-cell-center" style="font-weight: bold;">' . $totalRecords . ' Catatan</td>
                        <td class="tbl-cell-center">100%</td>
                    </tr>
                    <tr>
                        <td class="tbl-cell">Kehadiran Tepat Waktu (Disiplin)</td>
                        <td class="tbl-cell-center status-hadir">' . $totalHadir . ' Kali</td>
                        <td class="tbl-cell-center status-hadir">' . $disciplineRate . '%</td>
                    </tr>
                    <tr>
                        <td class="tbl-cell">Keterlambatan Jam Masuk</td>
                        <td class="tbl-cell-center status-terlambat">' . $totalTerlambat . ' Kali</td>
                        <td class="tbl-cell-center status-terlambat">' . $latePct . '%</td>
                    </tr>
                    <tr>
                        <td class="tbl-cell">Izin & Sakit Resmi Terverifikasi</td>
                        <td class="tbl-cell-center status-izin">' . $totalIzin . ' Hari</td>
                        <td class="tbl-cell-center status-izin">' . $leavePct . '%</td>
                    </tr>
                    <tr style="background-color: #f8fafc;">
                        <td class="tbl-cell" style="font-weight: bold;">Tingkat Kedisiplinan Unit</td>
                        <td colspan="2" class="tbl-cell-center" style="font-weight: bold; font-size: 11pt; color: #1e3a8a;">' . $disciplineRate . '%</td>
                    </tr>
                </tbody>
            </table>

            <table border="0"><tr><td colspan="9" style="height: 15px;"></td></tr></table>

            <!-- Tabel Detail Presensi -->
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr class="tbl-header">
                        <th style="width: 45px;">NO</th>
                        <th style="width: 100px;">TANGGAL</th>
                        <th style="width: 220px; text-align: left;">NAMA PEGAWAI</th>
                        <th style="width: 140px;">NIP / NIDN</th>
                        <th style="width: 180px; text-align: left;">JABATAN</th>
                        <th style="width: 100px;">JAM MASUK</th>
                        <th style="width: 100px;">JAM PULANG</th>
                        <th style="width: 110px;">STATUS</th>
                        <th style="width: 220px; text-align: left;">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>';

        $no = 1;
        foreach ($attendances as $att) {
            $statusClass = 'status-hadir';
            if ($att->status === 'terlambat') $statusClass = 'status-terlambat';
            elseif ($att->status === 'izin' || $att->status === 'sakit') $statusClass = 'status-izin';
            elseif ($att->status === 'alpa') $statusClass = 'status-alpa';

            $html .= '<tr>
                <td class="tbl-cell-center">' . $no++ . '</td>
                <td class="tbl-cell-center">' . date('d/m/Y', strtotime($att->date)) . '</td>
                <td class="tbl-cell" style="font-weight: bold;">' . htmlspecialchars($att->employee->name ?? '-') . '</td>
                <td class="tbl-cell-center" style="mso-number-format:\'\@\';">' . htmlspecialchars($att->employee->nip_nidn ?? '-') . '</td>
                <td class="tbl-cell">' . htmlspecialchars($att->employee->position ?? '-') . '</td>
                <td class="tbl-cell-center">' . ($att->time_in ? date('H:i:s', strtotime($att->time_in)) . ' WIB' : '-') . '</td>
                <td class="tbl-cell-center">' . ($att->time_out ? date('H:i:s', strtotime($att->time_out)) . ' WIB' : '-') . '</td>
                <td class="tbl-cell-center ' . $statusClass . '">' . ucfirst($att->status) . '</td>
                <td class="tbl-cell">' . htmlspecialchars($att->notes ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
        </body>
        </html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
