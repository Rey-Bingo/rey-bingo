<?php
namespace App\Services;

use CodeIgniter\Config\Services;

class GameState
{
    /** Genera una clave de caché segura (solo [A-Za-z0-9_.-]) */
    private static function key(string $gameId): string
    {
        $safeId = preg_replace('/[^A-Za-z0-9_.-]/', '_', $gameId); // evita {}()/\@:
        return 'game_' . $safeId;
    }

    /** Estado por defecto de una partida */
    private static function defaultState(): array
    {
        return [
            'drawn'   => [],   // int[]
            'stopped' => false,
            'players' => [],   // playerId => ['cards' => [ ['id'=>string, 'numbers'=>int[]], ... ]]
            'winner'  => null, // ['playerId'=>..., 'cardId'=>..., 'at'=>...]
        ];
    }

    /** Carga el estado desde caché (o lo inicializa) */
    private static function load(string $gameId): array
    {
        $cache = Services::cache();
        $key   = self::key($gameId);
        $state = $cache->get($key);
        if (!is_array($state)) {
            $state = self::defaultState();
            $cache->save($key, $state, 0); // 0 = sin expiración
        }
        return $state;
    }

    /** Guarda el estado en caché */
    private static function save(string $gameId, array $state): void
    {
        Services::cache()->save(self::key($gameId), $state, 0);
    }

    /** Snapshot mínimo para clientes */
    public static function snapshot(string $gameId): array
    {
        $r = self::load($gameId);
        return [
            'drawn'   => array_values(array_unique($r['drawn'])),
            'stopped' => (bool)$r['stopped'],
            'winner'  => $r['winner'],
        ];
    }

    /** Reinicia la partida */
    public static function reset(string $gameId): void
    {
        self::save($gameId, self::defaultState());
    }

    /** Registra o actualiza los cartones de un jugador */
    public static function join(string $gameId, string $playerId, array $cards): void
    {
        $r = self::load($gameId);
        $r['players'][$playerId] = ['cards' => $cards];
        self::save($gameId, $r);
    }

    /** Marca un número emitido por el admin */
    public static function draw(string $gameId, int $n): array
    {
        $r = self::load($gameId);
        if ($r['stopped']) {
            return self::snapshot($gameId);
        }
        if ($n >= 1 && $n <= 75 && !in_array($n, $r['drawn'], true)) {
            $r['drawn'][] = $n;
            self::save($gameId, $r);
        }
        return self::snapshot($gameId);
    }

    /**
     * Convierte array de números del cartón a grid 5x5 con FREE en el centro
     */
    private static function numbersToGrid(array $numbers): array
    {
        $grid = [];
        $index = 0;
        
        for ($row = 0; $row < 5; $row++) {
            for ($col = 0; $col < 5; $col++) {
                if ($row === 2 && $col === 2) {
                    // Centro siempre es FREE
                    $grid[$row][$col] = 'FREE';
                } else {
                    $grid[$row][$col] = $numbers[$index] ?? 0;
                    $index++;
                }
            }
        }
        
        return $grid;
    }

    /**
     * Verifica si hay BINGO en un cartón (línea completa)
     */
    private static function checkBingoPatterns(array $grid, array $drawnNumbers): array
    {
        $drawnSet = array_flip($drawnNumbers);
        $totalMatches = 0;
        
        // Función para verificar si una posición está marcada
        $isMarked = function($value) use ($drawnSet) {
            return $value === 'FREE' || isset($drawnSet[$value]);
        };

        // Contar total de matches para información
        for ($row = 0; $row < 5; $row++) {
            for ($col = 0; $col < 5; $col++) {
                if ($isMarked($grid[$row][$col])) {
                    $totalMatches++;
                }
            }
        }

        // Verificar filas horizontales
        for ($row = 0; $row < 5; $row++) {
            $complete = true;
            for ($col = 0; $col < 5; $col++) {
                if (!$isMarked($grid[$row][$col])) {
                    $complete = false;
                    break;
                }
            }
            if ($complete) {
                return [
                    'hasBingo' => true,
                    'type' => 'row',
                    'pattern' => "Fila " . ($row + 1),
                    'matches' => $totalMatches,
                    'required' => 5
                ];
            }
        }

        // Verificar columnas verticales
        for ($col = 0; $col < 5; $col++) {
            $complete = true;
            for ($row = 0; $row < 5; $row++) {
                if (!$isMarked($grid[$row][$col])) {
                    $complete = false;
                    break;
                }
            }
            if ($complete) {
                $columnNames = ['B', 'I', 'N', 'G', 'O'];
                return [
                    'hasBingo' => true,
                    'type' => 'column',
                    'pattern' => "Columna " . $columnNames[$col],
                    'matches' => $totalMatches,
                    'required' => 5
                ];
            }
        }

        // Verificar diagonal principal (\)
        $diagonalComplete = true;
        for ($i = 0; $i < 5; $i++) {
            if (!$isMarked($grid[$i][$i])) {
                $diagonalComplete = false;
                break;
            }
        }
        if ($diagonalComplete) {
            return [
                'hasBingo' => true,
                'type' => 'diagonal',
                'pattern' => 'Diagonal principal',
                'matches' => $totalMatches,
                'required' => 5
            ];
        }

        // Verificar diagonal secundaria (/)
        $diagonalComplete = true;
        for ($i = 0; $i < 5; $i++) {
            if (!$isMarked($grid[$i][4 - $i])) {
                $diagonalComplete = false;
                break;
            }
        }
        if ($diagonalComplete) {
            return [
                'hasBingo' => true,
                'type' => 'diagonal',
                'pattern' => 'Diagonal secundaria',
                'matches' => $totalMatches,
                'required' => 5
            ];
        }

        // Verificar cartón completo (blackout)
        if ($totalMatches === 25) {
            return [
                'hasBingo' => true,
                'type' => 'blackout',
                'pattern' => 'Cartón completo',
                'matches' => $totalMatches,
                'required' => 25
            ];
        }

        // No hay BINGO
        return [
            'hasBingo' => false,
            'type' => 'none',
            'pattern' => 'Sin línea completa',
            'matches' => $totalMatches,
            'required' => 5 // Mínimo para cualquier línea
        ];
    }

    /**
     * Intenta cantar Bingo.
     * Ahora verifica patrones reales de BINGO (líneas completas)
     */
    public static function claimBingo(string $gameId, string $playerId, string $cardId): array
    {
        $r = self::load($gameId);

        // Valores por defecto
        $check = [
            'matches' => 0, 
            'required' => 5, 
            'valid' => false,
            'pattern' => 'Sin cartón encontrado'
        ];

        if ($r['stopped']) {
            $snap = self::snapshot($gameId);
            $snap['check'] = $check;
            return $snap;
        }

        // Buscar el cartón del jugador
        $cards = $r['players'][$playerId]['cards'] ?? [];
        $found = null;
        foreach ($cards as $c) {
            if (($c['id'] ?? '') === $cardId) { 
                $found = $c; 
                break; 
            }
        }

        if (!$found) {
            $snap = self::snapshot($gameId);
            $snap['check'] = $check;
            return $snap;
        }

        // Convertir números del cartón a grid 5x5
        $numbers = $found['numbers'] ?? [];
        $grid = self::numbersToGrid($numbers);
        
        // Verificar patrones de BINGO
        $bingoResult = self::checkBingoPatterns($grid, $r['drawn']);
        
        $check = [
            'matches' => $bingoResult['matches'],
            'required' => $bingoResult['required'],
            'valid' => $bingoResult['hasBingo'],
            'pattern' => $bingoResult['pattern'],
            'type' => $bingoResult['type']
        ];

        // Si es válido, marcar como ganador
        if ($bingoResult['hasBingo']) {
            $r['stopped'] = true;
            $r['winner'] = [
                'playerId' => $playerId,
                'cardId' => $cardId,
                'pattern' => $bingoResult['pattern'],
                'type' => $bingoResult['type'],
                'at' => date('c'),
            ];
            self::save($gameId, $r);
        }

        $snap = self::snapshot($gameId);
        $snap['check'] = $check;
        
        // Si hay ganador, incluirlo en la respuesta
        if ($bingoResult['hasBingo']) {
            $snap['winner'] = $r['winner'];
        }
        
        return $snap;
    }
}
