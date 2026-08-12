<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ValorantMatch;

class ValorantMatchController extends Controller
{

    // NOVO MÉTODO: Carrega a tela principal com paginação
    public function index()
    {
        // Puxa as partidas do banco, ordenando da mais recente para a mais antiga
        // O "paginate(5)" já faz toda a mágica da paginação do Laravel!
        $partidas = ValorantMatch::orderBy('created_at', 'desc')->paginate(5);
        
        // Envia a variável $partidas para a View que vamos criar chamada 'partidas'
        return view('partidas', compact('partidas'));
    }

    public function syncMatches()
    {
        // 1. Fazendo a requisição para a API da comunidade
        $resposta = Http::withHeaders([
            'Authorization' => env('HENRIK_VALORANT_API_KEY')
            ])->get('https://api.henrikdev.xyz/valorant/v3/matches/br/Harriison/BR1?mode=competitive');
        
        // Verificando se a API retornou sucesso
        if ($resposta->successful()) {
            $partidas = $resposta->json('data');

            // 2. Percorrendo cada partida retornada pela API
            foreach ($partidas as $partida) {
                
                // Procurando os seus dados dentro da lista de jogadores da partida
                $meusDados = null;
                foreach ($partida['players']['all_players'] as $jogador) {
                    if (strtolower($jogador['name']) === strtolower('Harriison')) {
                        $meusDados = $jogador;
                        break;
                    }
                }

                // 3. Se encontrou você na partida, salva no banco!
                if ($meusDados) {
                    ValorantMatch::updateOrCreate(
                        // O Laravel procura por essa coluna para ver se já existe:
                        ['match_id' => $partida['metadata']['matchid']],
                        
                        // Se não existir, ele preenche o resto com esses dados:
                        [
                            'map' => $partida['metadata']['map'],
                            'agent' => $meusDados['character'],
                            'kills' => $meusDados['stats']['kills'],
                            'deaths' => $meusDados['stats']['deaths'],
                            'assists' => $meusDados['stats']['assists'],
                            // A API retorna quem venceu o jogo (Red ou Blue). 
                            // Nós comparamos para saber se o time vencedor é o seu.
                            'result' => $partida['teams']['red']['has_won'] && $meusDados['team'] === 'Red' || $partida['teams']['blue']['has_won'] && $meusDados['team'] === 'Blue' ? 'Vitória' : 'Derrota'
                        ]
                    );
                }
            }

            return redirect('/');
        }

        return "Erro ao buscar dados na API.";
    }
}