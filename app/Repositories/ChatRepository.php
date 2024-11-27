<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Repositories;

use App\Events\Chatter;
use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Http\Resources\ChatMessageResource;
use App\Models\Bot;
use App\Models\Chatroom;
use App\Models\ChatStatus;
use App\Models\Message;
use App\Models\User;
use App\Models\UserAudible;
use App\Models\UserEcho;
use Illuminate\Support\Str;

class ChatRepository
{
    /**
     * ChatRepository Constructor.
     */
    public function __construct(private readonly Message $message, private readonly Chatroom $chatroom, private readonly ChatStatus $chatStatus, private readonly User $user, private readonly Bot $bot, private readonly UserEcho $userEcho, private readonly UserAudible $userAudible)
    {
    }

    public function config(): mixed
    {
        return config('chat');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Bot>
     */
    public function bots(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->bot->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, UserEcho>
     */
    public function echoes(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userEcho->with([
            'bot',
            'user',
            'target',
            'room',
        ])
            ->where('user_id', '=', $userId)
            ->oldest('id')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, UserAudible>
     */
    public function audibles(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userAudible->with([
            'bot',
            'user',
            'target',
            'room',
        ])
            ->where('user_id', '=', $userId)
            ->latest()
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Chatroom>
     */
    public function rooms(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->chatroom->all();
    }

    public function roomFindOrFail(int $id): Chatroom
    {
        return $this->chatroom->findOrFail($id);
    }

    public function message(int $userId, int $roomId, string $message, ?int $receiver = null, ?int $bot = null): Message
    {
        if ($this->user->find($userId)->settings?->censor) {
            $message = $this->censorMessage($message);
        }

        $message = $this->htmlifyMessage($message);

        $message = $this->message->create([
            'user_id'     => $userId,
            'chatroom_id' => $roomId,
            'message'     => $message,
            'receiver_id' => $receiver,
            'bot_id'      => $bot,
        ]);

        $this->checkMessageLimits($roomId);

        broadcast(new MessageSent($message));

        return $message;
    }

    public function botMessage(int $botId, int $roomId, string $message, ?int $receiver = null): void
    {
        $user = $this->user->find($receiver);

        if ($user->settings?->censor) {
            $message = $this->censorMessage($message);
        }

        $message = $this->htmlifyMessage($message);
        $save = $this->message->create([
            'bot_id'      => $botId,
            'user_id'     => 1,
            'chatroom_id' => 0,
            'message'     => $message,
            'receiver_id' => $receiver,
        ]);

        $message = Message::with([
            'bot',
            'user.group',
            'user.chatStatus',
            'receiver.group',
            'receiver.chatStatus',
        ])->find($save->id);

        event(new Chatter('new.bot', $receiver, new ChatMessageResource($message)));
        event(new Chatter('new.ping', $receiver, ['type' => 'bot', 'id' => $botId]));
        $message->delete();
    }

    public function privateMessage(int $userId, int $roomId, string $message, ?int $receiver = null, ?int $bot = null, ?bool $ignore = null): Message
    {
        if ($this->user->find($userId)->settings?->censor) {
            $message = $this->censorMessage($message);
        }

        $message = $this->htmlifyMessage($message);

        $save = $this->message->create([
            'user_id'     => $userId,
            'chatroom_id' => 0,
            'message'     => $message,
            'receiver_id' => $receiver,
            'bot_id'      => $bot,
        ]);

        $message = Message::with([
            'bot',
            'user.group',
            'user.chatStatus',
            'receiver.group',
            'receiver.chatStatus',
        ])->find($save->id);

        if ($ignore != null) {
            event(new Chatter('new.message', $userId, new ChatMessageResource($message)));
        }

        event(new Chatter('new.message', $receiver, new ChatMessageResource($message)));

        if ($receiver != 1) {
            event(new Chatter('new.ping', $receiver, ['type' => 'target', 'id' => $userId]));
        }

        return $message;
    }

    public function deleteMessage(int $id): void
    {
        $message = $this->message->find($id);

        if ($message) {
            broadcast(new MessageDeleted($message));

            $message->delete();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function messages(int $roomId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->message->with([
            'bot',
            'user.group',
            'chatroom',
            'user.chatStatus',
            'receiver.group',
            'receiver.chatStatus',
        ])->where(function ($query) use ($roomId): void {
            $query->where('chatroom_id', '=', $roomId);
            $query->where('chatroom_id', '!=', 0);
        })
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function botMessages(int $senderId, int $botId): \Illuminate\Database\Eloquent\Collection
    {
        $systemUserId = User::where('username', 'System')->sole()->id;

        return $this->message->with([
            'bot',
            'user.group',
            'chatroom',
            'user.chatStatus',
            'receiver.group',
            'receiver.chatStatus',
        ])->where(function ($query) use ($senderId, $systemUserId): void {
            $query->whereRaw('(user_id = ? and receiver_id = ?)', [$senderId, $systemUserId])->orWhereRaw('(user_id = ? and receiver_id = ?)', [$systemUserId, $senderId]);
        })->where('bot_id', '=', $botId)
            ->where('chatroom_id', '=', 0)
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Message>
     */
    public function privateMessages(int $senderId, int $targetId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->message->with([
            'bot',
            'user.group',
            'chatroom',
            'user.chatStatus',
            'receiver.group',
            'receiver.chatStatus',
        ])->where(function ($query) use ($senderId, $targetId): void {
            $query->whereRaw('(user_id = ? and receiver_id = ?)', [$senderId, $targetId])->orWhereRaw('(user_id = ? and receiver_id = ?)', [$targetId, $senderId]);
        })
            ->where('chatroom_id', '=', 0)
            ->latest('id')
            ->limit(config('chat.message_limit'))
            ->get();
    }

    public function checkMessageLimits(int $roomId): void
    {
        $messages = $this->messages($roomId);
        $limit = config('chat.message_limit');
        $count = $messages->count();

        // Lets purge all old messages and keep the database to the limit settings
        if ($count > $limit) {
            for ($x = 1; $x <= $count - $limit; $x++) {
                $message = $messages->last();
                echo $message['id']."\n";

                $message = $this->message->find($message->id);

                if ($message->receiver_id === null) {
                    $message->delete();
                }
            }
        }
    }

    public function systemMessage(string $message, ?int $bot = null): static
    {
        $systemUserId = User::where('username', 'System')->first()->id;

        if ($bot) {
            $this->message($systemUserId, $this->systemChatroom(), $message, null, $bot);
        } else {
            $systemBotId = Bot::where('command', 'systembot')->first()->id;

            $this->message($systemUserId, $this->systemChatroom(), $message, null, $systemBotId);
        }

        return $this;
    }

    public function systemChatroom(int|Chatroom|string|null $room = null): int
    {
        $config = config('chat.system_chatroom');

        if ($room !== null) {
            if ($room instanceof Chatroom) {
                $room = $room->id;
            } elseif (\is_int($room)) {
                $room = $this->chatroom->findOrFail($room)->id;
            } else {
                $room = $this->chatroom->whereName($room)->first()->id;
            }
        } elseif (\is_int($config)) {
            $room = $this->chatroom->findOrFail($config)->id;
        } else {
            $room = $this->chatroom->whereName($config)->first()->id;
        }

        return $room;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ChatStatus>
     */
    public function statuses(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->chatStatus->all();
    }

    public function status(int|User $user): ?ChatStatus
    {
        $status = null;

        if ($user instanceof User) {
            $status = $this->chatStatus->where('user_id', '=', $user->id)->first();
        }

        if (\is_int($user)) {
            $status = $this->chatStatus->where('user_id', '=', $user)->first();
        }

        return $status;
    }

    public function statusFindOrFail(int $id): ChatStatus
    {
        return $this->chatStatus->findOrFail($id);
    }

    protected function censorMessage(string $message): string
    {
        foreach (config('censor.redact') as $word) {
            if (preg_match(\sprintf('/\b%s(?=[.,]|$|\s)/mi', $word), (string) $message)) {
                $message = str_replace($word, \sprintf("<span class='censor'>%s</span>", $word), (string) $message);
            }
        }

        foreach (config('censor.replace') as $word => $rword) {
            if (Str::contains($message, $word)) {
                $message = str_replace($word, $rword, (string) $message);
            }
        }

        return $message;
    }

    protected function htmlifyMessage(string $message): string
    {
        return $message;
    }
}
