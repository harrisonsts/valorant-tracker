<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ValorantMatch;
use Carbon\Carbon;

class ValorantMatchController extends Controller
{

    // Carrega a tela principal com paginação
    public function index()
    {
        // Puxa as partidas do banco, ordenando da mais recente para a mais antiga
        // O "paginate(5)" faz a paginação do Laravel!
        $partidas = ValorantMatch::orderBy('played_at', 'desc')->paginate(5);
        
        // Envia a variável $partidas para a View 'partidas'
        return view('partidas', compact('partidas'));
    }

    public function syncMatches()
    {
        // 1. Fazendo a requisição para a API
        $resposta = Http::withHeaders([
            'Authorization' => env('HENRIK_VALORANT_API_KEY')
            ])->get('https://api.henrikdev.xyz/valorant/v3/matches/br/Harriison/BR1?mode=competitive&size=10');
        
        // Verificando se a API retornou sucesso
        if ($resposta->successful()) {
            $partidas = $resposta->json('data');

            // 2. Percorrendo cada partida retornada pela API
            foreach ($partidas as $partida) {
                
                // Procurando dados dentro da lista de jogadores da partida
                $meusDados = null;
                foreach ($partida['players']['all_players'] as $jogador) {
                    if (strtolower($jogador['name']) === strtolower('Harriison')) {
                        $meusDados = $jogador;
                        break;
                    }
                }

                // 3. Se encontrou o jogador na partida, salva no banco!
                if ($meusDados) {

                    $totalTiros = $meusDados['stats']['bodyshots']
                        + $meusDados['stats']['legshots']
                        + $meusDados['stats']['headshots'];

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
                            // Comparamos para saber se o time vencedor é o seu.
                            'result' => 
                            $partida['teams']['red']['has_won'] && $meusDados['team'] === 'Red' 
                            || 
                            $partida['teams']['blue']['has_won'] && $meusDados['team'] === 'Blue' ? 'Vitória' : 'Derrota',

                            'hs' => $totalTiros > 0
                                ? ($meusDados['stats']['headshots'] / $totalTiros) * 100
                                : 0,

                            'played_at' => Carbon::createFromTimestampUTC(
                                $partida['metadata']['game_start']
                            )->setTimezone('America/Sao_Paulo'),

                        ]
                    );
                }
            }

            return redirect('/');
        }

        return "Erro ao buscar dados na API.";
    }
}