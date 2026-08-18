<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Valorant Tracker</title>
    <!-- Adicionando Tailwind via CDN para um design instantâneo -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 p-8 font-sans">

    <div class="max-w-5xl mx-auto">
        
        <!-- Cabeçalho e Botão -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-red-500">Histórico de Partidas</h1>
            
            <!-- Esse botão chama a nossa rota /sync que pega os dados novos -->
            <a href="/sync" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                Sincronizar Partidas
            </a>
        </div>

        <!-- Tabela -->
        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-700 text-gray-300">
                    <tr>
                        <th class="p-4 border-b border-gray-600">Mapa</th>
                        <th class="p-4 border-b border-gray-600">Agente</th>
                        <th class="p-4 border-b border-gray-600">K / D / A</th>
                        <th class="p-4 border-b border-gray-600">Resultado</th>
                        <th class="p-4 border-b border-gray-600">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <!-- O Blade varre a variável que mandamos do Controller -->
                    @foreach ($partidas as $partida)
                    <tr class="hover:bg-gray-750 transition">
                        <td class="p-4">{{ $partida->map }}</td>
                        <td class="p-4">{{ $partida->agent }}</td>
                        <td class="p-4 font-mono">{{ $partida->kills }} / {{ $partida->deaths }} / {{ $partida->assists }}</td>
                        
                        <!-- Condicional simples para colorir Vitória e Derrota -->
                        <td class="p-4 font-bold {{ $partida->result == 'Vitória' ? 'text-green-400' : 'text-red-400' }}">
                            {{ $partida->result }}
                        </td>

                        <td class="p-4">{{ $partida->played_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação com Botão de Última Página -->
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            
            <!-- Paginação Padrão do Laravel -->
            <div class="w-full overflow-x-auto">
                {{ $partidas->links() }}
            </div>
            
            <!-- Botão Personalizado de Última Página -->
            @if ($partidas->currentPage() < $partidas->lastPage())
                <div>
                    <a href="{{ $partidas->url($partidas->lastPage()) }}" 
                       class="inline-block bg-gray-700 hover:bg-gray-600 text-gray-200 font-bold py-2 px-4 rounded transition whitespace-nowrap">
                        ({{ $partidas->lastPage() }}) &raquo;
                    </a>
                </div>
            @endif

        </div>

    </div>

</body>
</html>