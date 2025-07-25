<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Notifications\NotifySite;
use App\Notifications\SendMessageEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class Notifier
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function announcement(User $client, User $owner, Group $group, string $templatePath): void
    {
        $recipients = [
            'owner' => $owner,
            'client' => $client,
        ];

        $params = Config::get("notifications.{$templatePath}.params", []);
        $placeholders = array_map(fn($key) => ':' . $key, $params);
        $replacements = $this->buildDynamicReplacements($placeholders, $client, $owner, $group);

        // === TELEGRAM === //
        $telegramTemplates = Config::get("notifications.{$templatePath}.telegram", []);
        foreach ($recipients as $role => $user) {
            if ($user->notify_tg && $user->chat_id_telegram) {
                $template = $telegramTemplates[$role]['message'] ?? null;
                if ($template) {
                    $escaped = [];
                    foreach ($replacements as $key => $value) {
                        $escaped[$key] = in_array($key, [':url', ':link_to_pdf']) ? $value : $this->escapeMarkdownV2($value);
                    }
                    $message = strtr($template, $escaped);
                    $this->telegramService->sendMessage($user->chat_id_telegram, $message);
                }
            }
        }

        // === EMAIL === //
        $emailTemplates = Config::get("notifications.{$templatePath}.email", []);
        foreach ($recipients as $role => $user) {
            if ($user->notify_email) {
                $subject = $emailTemplates[$role]['subject'] ?? 'Уведомление';
                $message = $emailTemplates[$role]['message'] ?? null;

                if ($message) {
                    $finalMessage = strtr($message, $replacements);
                    $finalSubject = strtr($subject, $replacements);
                    $user->notify(new SendMessageEmail($finalSubject, $finalMessage));
                }
            }
        }

        // === SITE === //
        $siteTemplates = Config::get("notifications.{$templatePath}.site", []);
        foreach ($recipients as $role => $user) {
            if ($user->notify_site) {
                $template = $siteTemplates[$role] ?? null;

                if ($template) {
                    $title = $template['title'] ?? '';
                    $message = $template['message'] ?? '';

                    $finalTitle = strtr($title, $replacements);
                    $finalMessage = strtr($message, $replacements);

                    $link = $replacements[':url'] ?? '#';

                    $user->notify(new NotifySite($finalTitle, $finalMessage, $link));
                }
            }
        }

    }

    private function escapeMarkdownV2(string $text): string
    {
        return preg_replace('/([_\*\[\]\(\)~>`#+\-=|{}\.!\\\])/', '\\\$1', $text);
    }

    private function buildDynamicReplacements(array $keys, User $client, User $owner, Group $group): array
    {
        $dateTime = Carbon::parse($group->date . ' ' . $group->time);
        $formattedDate = $dateTime->translatedFormat('j F Y') . ' в ' . $dateTime->format('H:i');
        $url = route('group', ['group' => $group->id]);

        $map = [
            ':client' => $client->name . ' ' . $client->nickname,
            ':owner' => $owner->name . ' ' . $owner->nickname,
            ':title_group' => $group->title,
            ':date_time' => $formattedDate,
            ':count' => ($group->countUser()) . '/' . $group->count_people,
            ':url' => $url,
            ':group_id' => $group->id,
            ':client_id' => $client->id,
        ];

        return array_intersect_key($map, array_flip($keys));
    }
}
