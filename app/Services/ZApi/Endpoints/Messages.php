<?php

namespace App\Services\ZApi\Endpoints;

/**
 * Class Messages
 *
 * Handles messaging endpoints.
 */
class Messages extends BaseEndpoint
{
    /**
     * Send a text message.
     *
     * @param string $phone
     * @param string $message
     * @param int|null $delay_message
     * @param int|null $delay_typing
     * @param string|null $edit_message_id
     *
     * @return array
     */
    public function sendText(
        string $phone,
        string $message,
        ?int $delay_message = null,
        ?int $delay_typing = null,
        ?string $edit_message_id = null
    ): array {
        return $this->service->api->post('/send-text', [
            'phone' => $phone,
            'message' => $message,
            'delayMessage' => $delay_message,
            'delayTyping' => $delay_typing,
            'editMessageId' => $edit_message_id,
        ])->json();
    }

    public function sendImage(string $phone, string $image, ?string $caption = null, ?string $message_id = null, ?int $delay_message = null, ?string $view_once = null): array
    {
        return $this->service->api->post('/send-image', [
            'phone' => $phone,
            'image' => $image,
            'caption' => $caption,
            'messageId' => $message_id,
            'delayMessage' => $delay_message,
            'viewOnce' => $view_once,
        ])->json();
    }
}
