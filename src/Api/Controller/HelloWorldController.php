<?php

namespace Cobbz\FlarumExtMoneyBatchTransfer\Api\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use AntoineFr\Money\Event\MoneyUpdated;
use Carbon\Carbon;

use Ziven\transferMoney\Serializer\TransferMoneySerializer;
use Ziven\transferMoney\Model\TransferMoney;
use Ziven\transferMoney\Notification\TransferMoneyBlueprint;

use Flarum\Extension\ExtensionManager;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Locale\Translator;
use Flarum\Foundation\ValidationException;



class HelloWorldController implements RequestHandlerInterface
{
    protected $dispatcher;
    protected $notifications;
    protected $validation;
    protected $extensions;
    protected $translator;

    public function __construct(Dispatcher $dispatcher, NotificationSyncer $notifications, Factory $validation, ExtensionManager $extensions, Translator $translator)
    {
        $this->dispatcher = $dispatcher;
        $this->notifications = $notifications;
        $this->validation = $validation;
        $this->extensions = $extensions;
        $this->translator = $translator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        // api的header中有说明
        $actor = RequestUtil::getActor($request);
        $actor->assertAdmin();
        $currentUserID = $actor->id;
        $query = User::query();

        $requestData = $request->getParsedBody()['data']['attributes'];
        $amount = floatval($requestData['amount']);
        $notify = true;
        $dryRun = false;
        $message = trim($requestData['message']);

        $selectedUsers = $requestData['selectedUsers'];
        $moneyTransferTotalUser = count($selectedUsers);
        $errorMessage = '';
        $lastActivity = (string)$requestData['lastActivity'];
        $lastActivityDate = Carbon::parse($lastActivity);

        if (!isset($amount) || $amount <= 0 || $moneyTransferTotalUser === 0) {
            $errorMessage = 'ziven-transfer-money.forum.transfer-error';
        }

        // 接口传过来的email，需要搜索这些用户
        $query->whereIn('email', $selectedUsers);
        $count = $query->count();
        if (!$dryRun) {
            $recipients = [];
            $query->each(function (User $user) use ($amount, $notify, $message, &$recipients,$currentUserID) {
                $transferMoney = new TransferMoney();
                $transferMoney->from_user_id = $currentUserID;
                $transferMoney->target_user_id = $user->id;
                $transferMoney->transfer_money_value = $amount;
                $transferMoney->assigned_at = Carbon::now("Asia/Shanghai");

                if(!empty($message)){
                    $transferMoney->notes = $message;
                }
                $transferMoney->save();
                $user->money += $amount;
                $user->save();
                $this->notifications->sync(new TransferMoneyBlueprint($transferMoney), [$user]);
            });
        }
        $this->validation->make(compact('amount', 'message'), [
            'amount' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:20000',
        ])->validate();


        if ($errorMessage !== "") {
            throw new ValidationException(['message' => $this->translator->trans($errorMessage)]);
        }

        return new JsonResponse([
            "data" => [
                "attributes" => [
                    "message" => $message,
                    "amount" => $amount,
                    "moneyTransferTotalUserNumber" => $count,
                ]
            ]
        ]);
    }
}
