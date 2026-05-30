<?php
declare(strict_types=1);

namespace App\Webhooks;

class Trello
{
    public function __construct(private readonly object $data)
    {
    }

    public function parseMessage(): string
    {
        if (!isset($this->data->action) || !isset($this->data->action->type)) {
            return '';
        }

        $action = $this->data->action;
        $actionType = $action->type ?? 'unknown';
        $memberName = $action->memberCreator->fullName ?? $action->memberCreator->username ?? 'Unknown User';
        $board = $this->data->model->name ?? 'Unknown Board';

        $message = '<b>' . $board . '</b> - ' . $actionType . PHP_EOL;
        $message .= 'By: <b>' . $memberName . '</b>' . PHP_EOL;

        return $this->buildActionMessage($message, $action, $actionType);
    }

    private function buildActionMessage(string $message, object $action, string $actionType): string
    {
        return match ($actionType) {
            'createCard' => $this->getCardCreatedMessage($message, $action),
            'updateCard' => $this->getCardUpdatedMessage($message, $action),
            'commentCard' => $this->getCardCommentMessage($message, $action),
            'updateCheckItemStateOnCard' => $this->getChecklistMessage($message, $action),
            default => $this->getDefaultMessage($message, $action),
        };
    }

    private function getCardCreatedMessage(string $message, object $action): string
    {
        $cardName = $action->data->card->name ?? 'Unknown Card';
        $listName = $action->data->list->name ?? 'Unknown List';

        $message .= 'List: <b>' . $listName . '</b>' . PHP_EOL;
        $message .= 'Card: <a href="' . ($action->data->card->url ?? '#') . '">' . $cardName . '</a>' . PHP_EOL;

        return $message;
    }

    private function getCardUpdatedMessage(string $message, object $action): string
    {
        $cardName = $action->data->card->name ?? 'Unknown Card';
        $message .= 'Card: <a href="' . ($action->data->card->url ?? '#') . '">' . $cardName . '</a>' . PHP_EOL;

        if (!empty($action->data->old)) {
            $message .= 'Changes: ';
            $changes = [];
            foreach ((array)$action->data->old as $key => $oldValue) {
                $changes[] = $key;
            }
            $message .= implode(', ', $changes) . PHP_EOL;
        }

        return $message;
    }

    private function getCardCommentMessage(string $message, object $action): string
    {
        $cardName = $action->data->card->name ?? 'Unknown Card';
        $comment = $action->data->text ?? '';

        $message .= 'Card: <a href="' . ($action->data->card->url ?? '#') . '">' . $cardName . '</a>' . PHP_EOL;
        $message .= 'Comment: ' . strip_tags($comment) . PHP_EOL;

        return $message;
    }

    private function getChecklistMessage(string $message, object $action): string
    {
        $cardName = $action->data->card->name ?? 'Unknown Card';
        $checkItemName = $action->data->checkItem->name ?? 'Unknown Item';
        $state = $action->data->checkItem->state ?? 'unknown';

        $message .= 'Card: <a href="' . ($action->data->card->url ?? '#') . '">' . $cardName . '</a>' . PHP_EOL;
        $message .= 'Checklist Item: <b>' . $checkItemName . '</b> - ' . $state . PHP_EOL;

        return $message;
    }

    private function getDefaultMessage(string $message, object $action): string
    {
        if (!empty($action->data->card->name)) {
            $message .= 'Card: <a href="' . ($action->data->card->url ?? '#') . '">' . $action->data->card->name . '</a>' . PHP_EOL;
        }

        if (!empty($action->data->text)) {
            $message .= 'Text: ' . strip_tags((string) $action->data->text) . PHP_EOL;
        }

        return $message;
    }
}
