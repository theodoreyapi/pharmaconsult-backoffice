<?php

namespace App\Http\Controllers;

use App\Models\Rechargements;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RechargementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $query = Rechargements::query()
            ->join('users_pharma', 'rechargements.username', '=', 'users_pharma.username')
            ->select('rechargements.*', 'users_pharma.first_name', 'users_pharma.last_name')
            ->where('rechargements.status', 'pending');

        // Recherche username ou transaction
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('rechargements.username', 'like', "%{$search}%")
                    ->orWhere('rechargements.transaction_id', 'like', "%{$search}%");
            });
        }

        // Méthode de paiement
        if ($request->filled('payment_method')) {

            $query->where(
                'rechargements.payment_method',
                $request->payment_method
            );
        }

        // Montant minimum
        if ($request->filled('min')) {

            $query->where(
                'rechargements.montant',
                '>=',
                $request->min
            );
        }

        // Montant maximum
        if ($request->filled('max')) {

            $query->where(
                'rechargements.montant',
                '<=',
                $request->max
            );
        }

        $rechargements = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view('wallet.rechargement', compact('rechargements'));
    }

    public function valider($id)
    {
        DB::beginTransaction();

        try {

            $rechargement = Rechargements::findOrFail($id);

            if ($rechargement->status != 'pending') {
                return back()->with('error', 'Ce rechargement a déjà été traité.');
            }

            $user = UsersPharma::where(
                'username',
                $rechargement->username
            )->firstOrFail();

            $user->amount += $rechargement->montant;
            $user->last_amount = $rechargement->montant;
            $user->save();

            $rechargement->status = 'success';
            $rechargement->save();

            DB::commit();

            return back()->with(
                'succes',
                'Rechargement validé avec succès.'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rechargement = Rechargements::findOrFail($id);

        $rechargement->delete();

        return back()->with(
            'succes',
            'Rechargement supprimé.'
        );
    }
}
