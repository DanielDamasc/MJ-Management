<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function gerarPmoc($id)
    {
        // Busca os clientes com os dados relacionados.
        $cliente = Client::with(['airConditioners.plan.tasks', 'address'])->findOrFail($id);

        // Pega o primeiro plano, filtra pra retirar os vazios.
        $plano = $cliente->airConditioners->pluck('plan')->filter()->first();

        // Faz algumas validações de dados necessários para gerar PDF.
        try {
            $this->validateRelations($cliente, $plano);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('notify-error', $e->getMessage());
        }

        // Carrega a view passando o cliente e o plano.
        $pdf = Pdf::loadView('pdf.pmoc', compact('cliente', 'plano'));

        // Abre o PDF no navegador ao invés de forçar o download direto.
        return $pdf->stream('PMOC-' . $cliente->cliente . '.pdf');
    }

    private function validateRelations($cliente, $plano)
    {
        if ($cliente->airConditioners->isEmpty()) {
            throw new Exception("O cliente deve ter equipamentos vinculados para realizar esta ação.");
        }

        if ($cliente->address == null) {
            throw new Exception("O cliente deve ter endereço vinculado para realizar esta ação.");
        }

        if (empty($cliente->inicio_pmoc)) {
            throw new Exception("O cliente deve ter a data de início do pmoc definida para realizar esta ação.");
        }

        if (!$plano) {
            throw new Exception("Os equipamentos do cliente devem ter plano de manutenção vinculado para realizar esta ação.");
        }
    }
}
