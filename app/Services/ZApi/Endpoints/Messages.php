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

    /**
     * Send an option list message.
     */
    public function sendOptionList(
        string $phone,
        string $message,
        string $title,
        string $buttonLabel,
        array $options
    ): Message {
        $response = $this->service->api->post('/send-option-list', [
            'phone' => $phone,
            'message' => $message,
            'optionList' => [
                'title' => $title,
                'buttonLabel' => $buttonLabel,
                'options' => $options,
            ],
        ])->json();

        return new Message($response);
    }
}
