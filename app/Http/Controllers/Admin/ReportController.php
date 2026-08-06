<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filtè ane ak mwa (si itilizatè a chwazi yo)
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));

        // Totèl jeneral
        $totalVentesGlobal = DB::table('commandes')->where('status', 'payé')->sum('total');
        $totalCommandesGlobal = DB::table('commandes')->count();

        // Lavant pou ane chwazi a
        $ventesAnnee = DB::table('commandes')
            ->whereYear('created_at', $selectedYear)
            ->where('status', 'payé')
            ->sum('total');

        $commandesAnneeCount = DB::table('commandes')
            ->whereYear('created_at', $selectedYear)
            ->count();

        // Lavant pou mwa ak ane chwazi a
        $ventesMois = DB::table('commandes')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->where('status', 'payé')
            ->sum('total');

        $commandesMoisCount = DB::table('commandes')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        // Estatistik pa mwa pou ane chwazi a (pou tablo rezime a)
        $monthlyStats = DB::table('commandes')
            ->select(
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('SUM(total) as total_ventes'),
                DB::raw('COUNT(*) as total_commandes')
            )
            ->whereYear('created_at', $selectedYear)
            ->where('status', 'payé')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Lis ane ki disponib nan baz done a pou filtè a
        $years = DB::table('commandes')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }

        return view('admin.reports.index', compact(
            'totalVentesGlobal',
            'totalCommandesGlobal',
            'ventesAnnee',
            'commandesAnneeCount',
            'ventesMois',
            'commandesMoisCount',
            'monthlyStats',
            'selectedYear',
            'selectedMonth',
            'years'
        ));
    }
}