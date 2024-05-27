<?php

namespace App\Http\Controllers;

use App\Enums\ChatbotStepsEnum;
use App\Handlers\StepHandlerInterface;
use App\Models\ChatbotHistory;
use App\Models\ChatbotUser;
use App\Services\ZApi\ChatbotService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function handle(Request $request)
    {
        $data = $request->all();
        $phone = $data['phone'];
        $name = $data['senderName'] ?? null;
        $photo = $data['photo'] ?? null;

        $user = ChatbotUser::firstOrCreate(['phone' => $phone], [
            'name' => $name,
            'photo' => $photo,
        ]);

        $history = ChatbotHistory::firstOrCreate([
            'chatbot_user_id' => $user->id,
            'step' => ChatbotStepsEnum::MENU,
        ]);

        if (isset($data['listResponseMessage'])) {
            $selectedOption = $data['listResponseMessage']['selectedRowId'];
            $this->updateStep($history, $selectedOption);
        }

        if (isset($data['text']['message'])) {
            $message = $data['text']['message'];
            $this->processMessage($history, $user, $message);
        } else {
            $this->processMessage($history, $user);
        }

        return response()->json(['status' => 'success']);
    }

    protected function updateStep(ChatbotHistory $history, string $selectedOption)
    {
        $step = match ($selectedOption) {
            '1' => ChatbotStepsEnum::MENU,
            '2' => ChatbotStepsEnum::LEGAL,
            '21' => ChatbotStepsEnum::LEGAL_LIST,
            '22' => ChatbotStepsEnum::LEGAL_LIST_INFO,
            '3' => ChatbotStepsEnum::FINANCIAL,
            '31' => ChatbotStepsEnum::FINANCIAL_LIST,
            '32' => ChatbotStepsEnum::FINANCIAL_LIST_INFO,
            '9' => ChatbotStepsEnum::HUMAN_RESOURCES,
            default => ChatbotStepsEnum::MENU,
        };

        $history->step = $step;
        $history->save();
    }

    protected function processMessage(ChatbotHistory $history, ChatbotUser $user, ?string $message = null)
    {
        $handler = $this->getHandler($history->step);
        $handler->handle($history, $user, $message);
    }

    protected function getHandler(ChatbotStepsEnum $step): StepHandlerInterface
    {
        return match ($step) {
            ChatbotStepsEnum::MENU => new \App\Handlers\MenuHandler($this->chatbotService),
            ChatbotStepsEnum::LEGAL => new \App\Handlers\LegalHandler($this->chatbotService),
            ChatbotStepsEnum::FINANCIAL => new \App\Handlers\FinancialHandler($this->chatbotService),
            default => new \App\Handlers\MenuHandler($this->chatbotService),
        };
    }
}
