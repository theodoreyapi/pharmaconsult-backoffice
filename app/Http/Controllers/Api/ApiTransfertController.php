<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTransfertController extends Controller
{
    /**
     * Liste toutes les transactions
     */
    public function index()
    {
        $transactions = DB::table('transactions')->get();
        return response()->json($transactions);
    }

    /**
     * Récupère une transaction spécifique
     */
    public function show($id)
    {
        $transaction = DB::table('transactions')->where('id', $id)->first();
        if (!$transaction) return response()->json(['message' => 'Transaction non trouvée'], 404);

        return response()->json($transaction);
    }

    /**
     * Crée une nouvelle transaction
     */
    public function store(Request $request)
    {
        $id = DB::table('transactions')->insertGetId([
            'wallet_id' => $request->walletId,
            'username' => $request->username,
            'amount' => $request->amount,
            'date' => $request->date ?? now(),
            'libelle' => $request->libelle ?? '',
            'designation' => $request->designation ?? '',
            'type_operation' => $request->typeOperation ?? 'unknown',
            'description' => $request->description ?? '',
            'name_of_second_party' => $request->nameOfSecondParty ?? '',
            'number_of_second_party' => $request->numberOfSecondParty ?? '',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['id' => $id, 'message' => 'Transaction créée']);
    }

    /**
     * Met à jour une transaction
     */
    public function update(Request $request, $id)
    {
        $updated = DB::table('transactions')->where('id', $id)->update([
            'wallet_id' => $request->walletId ?? DB::raw('wallet_id'),
            'username' => $request->username ?? DB::raw('username'),
            'amount' => $request->amount ?? DB::raw('amount'),
            'date' => $request->date ?? DB::raw('date'),
            'libelle' => $request->libelle ?? DB::raw('libelle'),
            'designation' => $request->designation ?? DB::raw('designation'),
            'type_operation' => $request->typeOperation ?? DB::raw('type_operation'),
            'description' => $request->description ?? DB::raw('description'),
            'name_of_second_party' => $request->nameOfSecondParty ?? DB::raw('name_of_second_party'),
            'number_of_second_party' => $request->numberOfSecondParty ?? DB::raw('number_of_second_party'),
            'updated_at' => now()
        ]);

        if (!$updated) return response()->json(['message' => 'Transaction non trouvée ou inchangée'], 404);

        return response()->json(['message' => 'Transaction mise à jour']);
    }

    /**
     * Supprime une transaction
     */
    public function destroy($id)
    {
        $deleted = DB::table('transactions')->where('id', $id)->delete();
        if (!$deleted) return response()->json(['message' => 'Transaction non trouvée'], 404);

        return response()->json(['message' => 'Transaction supprimée']);
    }

    /**
     * Liste toutes les transactions d'un utilisateur
     */
    public function transactionsByUser($username)
    {
        $transactions = DB::table('transactions')
            ->where('username', $username)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Liste toutes les transactions par type d'opération
     */
    public function transactionsByType($typeOperation)
    {
        $transactions = DB::table('transactions')
            ->where('type_operation', $typeOperation)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Liste toutes les transactions dans une période donnée
     */
    public function transactionsByDateRange(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $transactions = DB::table('transactions')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return response()->json($transactions);
    }
}
