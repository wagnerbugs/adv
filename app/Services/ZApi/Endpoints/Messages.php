<?php

namespace App\Services\ZApi\Endpoints;

use App\Services\ZApi\Entities\Message;

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
    ): Message {
        $response = $this->service->api->post('/send-text', [
            'phone' => $phone,
            'message' => $message,
            'delayMessage' => $delay_message,
            'delayTyping' => $delay_typing,
            'editMessageId' => $edit_message_id,
        ])->json();

        return new Message($response);
    }

    /**
     * Send an image.
     *
     * @param string $phone
     * @param string $image
     * @param string|null $caption
     * @param string|null $message_id
     * @param int|null $delay_message
     * @param string|null $view_once
     *
     * @return array
     */
    public function sendImage(
        string $phone,
        string $image,
        ?string $caption = null,
        ?string $message_id = null,
        ?int $delay_message = null,
        ?string $view_once = null
    ): Message {
        $response = $this->service->api->post('/send-image', [
            'phone' => $phone,
            'image' => $image,
            'caption' => $caption,
            'messageId' => $message_id,
            'delayMessage' => $delay_message,
            'viewOnce' => $view_once,
        ])->json();

        return new Message($response);
    }
}
