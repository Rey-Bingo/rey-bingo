<?php
namespace App\Controllers;

use App\Libraries\PusherFactory;
use App\Services\GameState;
use App\Models\BoardsModel;
use App\Models\NotificationsModel;
use App\Models\CartonsModel;
use App\Models\UsersModel;
use CodeIgniter\RESTful\ResourceController;

class GamesNew extends ResourceController
{
    protected $format = 'json';

    public function state(string $gameId)
    {
        return $this->respond(GameState::snapshot($gameId));
    }

    public function reset(string $gameId)
    {
        GameState::reset($gameId);
        $this->trigger($gameId, 'game:game_reset', []);
        return $this->respond(['ok' => true]);
    }

    public function join(string $gameId)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $playerId = $data['playerId'] ?? '';
        $cards    = $data['cards'] ?? [];

        if ($playerId === '' || !is_array($cards)) {
            return $this->failValidationErrors('playerId y cards requeridos');
        }

        foreach ($cards as &$card) {
            $card['id']      = (string)($card['id'] ?? '');
            $nums            = $card['numbers'] ?? [];
            $card['numbers'] = array_values(array_unique(array_map('intval', $nums)));
        }
        unset($card);

        GameState::join($gameId, $playerId, $cards);
        return $this->respond(['ok' => true]);
    }

    public function draw(string $gameId)
    {
        $payload = $this->request->getJSON(true);
        $n = (int)($payload['n'] ?? $this->request->getPost('n'));

        if ($n < 1 || $n > 75) {
            return $this->failValidationErrors('n debe estar entre 1 y 75');
        }

        $snap = GameState::draw($gameId, $n);

        // Guardar en base de datos
        $this->saveNumberToBoard($gameId, $n, false); // false = manual

        // Broadcast al canal del juego
        $this->trigger($gameId, 'game:number_drawn', [
            'n'     => $n,
            'drawn' => $snap['drawn'],
        ]);

        return $this->respond($snap);
    }

    // Nueva función para sorteo automático
    public function autoDraw(string $gameId)
    {
        $payload = $this->request->getJSON(true);
        $n = (int)($payload['n'] ?? $this->request->getPost('n'));

        if ($n < 1 || $n > 75) {
            return $this->failValidationErrors('n debe estar entre 1 y 75');
        }

        $snap = GameState::draw($gameId, $n);

        // Guardar en base de datos
        $this->saveNumberToBoard($gameId, $n, true); // true = automático

        // Broadcast al canal del juego
        $this->trigger($gameId, 'game:number_drawn', [
            'n'     => $n,
            'drawn' => $snap['drawn'],
            'auto'  => true, // Indicar que fue automático
        ]);

        return $this->respond($snap);
    }

    public function claimBingo(string $gameId)
    {
        $data     = $this->request->getJSON(true) ?? $this->request->getPost();
        $playerId = (string)($data['playerId'] ?? '');
        $cardId   = (string)($data['cardId'] ?? '');

        if ($playerId === '' || $cardId === '') {
            return $this->failValidationErrors('playerId y cardId requeridos');
        }

        $snap = GameState::claimBingo($gameId, $playerId, $cardId);

        // SIEMPRE avisar al panel que alguien cantó
        $this->trigger($gameId, 'game:bingo_claimed', [
            'playerId' => $playerId,
            'cardId'   => $cardId,
            'matches'  => $snap['check']['matches']  ?? null,
            'required' => $snap['check']['required'] ?? null,
            'valid'    => $snap['check']['valid']    ?? false,
            'at'       => date('c'),
        ]);

        // Si ganó, además avisar bingo_accepted
        if (!empty($snap['winner'])) {
            $this->trigger($gameId, 'game:bingo_accepted', $snap['winner'] + ['stopped' => true]);

            $modelNotifications = new NotificationsModel();
            $modelCartons = new CartonsModel();
            $modelUsers = new UsersModel();

            $currentUserId = session()->get('id');

            $usersFromCartons = $modelCartons->select('user')->where('game', $gameId)->where('user !=', $currentUserId)->groupBy('user')->findAll();

            $cartonUserIds = array_column($usersFromCartons, 'user');

            $admins = $modelUsers->select('id')->where('group', 1)->findAll();

            $adminIds = array_column($admins, 'id');

            $allUserIds = array_unique(array_merge($cartonUserIds, $adminIds));

            $userSing = $modelUsers->find(session()->get('id'));

            foreach ($allUserIds as $userId) {
                $notificationData = [
                    'user' => $userId,
                    'from' => $currentUserId,
                    'type' => 'sing',
                    'game' => $gameId,
                    'modality' => 'En una modalidad',
                    'title' => '🎉 ¡BINGO CANTADO!',
                    'message' => $userSing['firstname'] . ' ' . $userSing['lastname'] . ' ha cantado ¡BINGO! en la modalidad MODALIDAD DESCONOCIDA.',
                ];

                $modelNotifications->insert($notificationData);
            }
        }

        $snap['ok'] = true;
        return $this->respond($snap);
    }

    /**
     * Guardar número sorteado en la base de datos
     */
    private function saveNumberToBoard(string $gameId, int $number, bool $isAuto = false)
    {
        try {
            $boardsModel = new BoardsModel();
            
            $data = [
                'user'   => 'admin', // o el ID del usuario admin actual
                'game'   => $gameId,
                'number' => $number,
                'status' => 1,
                'isCRON' => $isAuto ? 1 : 0, // 1 si es automático, 0 si es manual
            ];

            $boardsModel->insert($data);
            
            log_message('info', "Número {$number} guardado en BD para juego {$gameId} (auto: " . ($isAuto ? 'sí' : 'no') . ")");
            
        } catch (\Exception $e) {
            log_message('error', "Error al guardar número en BD: " . $e->getMessage());
        }
    }

    /**
     * Publica en el canal del juego.
     */
    private function trigger(string $gameId, string $event, array $payload): void
    {
        $channel = 'private-game-' . $gameId;
        PusherFactory::make()->trigger($channel, $event, $payload);
    }
}
