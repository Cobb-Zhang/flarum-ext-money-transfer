<?php

namespace Cobbz\FlarumExtMoneyBatchTransfer\Api\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use AntoineFr\Money\Event\MoneyUpdated;
use Carbon\Carbon;

use Ziven\transferMoney\Model\TransferMoney;
use Ziven\transferMoney\Notification\TransferMoneyBlueprint;

use Flarum\Extension\ExtensionManager;
use Flarum\Http\RequestUtil;
use Flarum\Notification\NotificationSyncer;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\Locale\Translator;
use Flarum\Foundation\ValidationException;



class MoneyTransferController implements RequestHandlerInterface
{
    protected $dispatcher;
    protected $notifications;
    protected $extensions;
    protected $translator;

    public function __construct(Dispatcher $dispatcher,NotificationSyncer $notifications, ExtensionManager $extensions, Translator $translator)
    {
        $this->dispatcher = $dispatcher;
        $this->notifications = $notifications;
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
        $dryRun = false;
        $message = trim($requestData['message']);

        $selectedUsers = $requestData['selectedUsers'];
        $moneyTransferTotalUser = count($selectedUsers);
        $errorMessage = '';

        if (!isset($amount) || $amount <= 0 || $moneyTransferTotalUser === 0) {
            $errorMessage = 'ziven-transfer-money.forum.transfer-error';
        }

        // 接口传过来的email，需要搜索这些用户
        $query->whereIn('email', $selectedUsers);
        $count = $query->count();

        if (!$dryRun) {
            $query->each(function (User $user) use ($amount,$message, $currentUserID) {
                $transferMoney = new TransferMoney();
                $transferMoney->from_user_id = $currentUserID;
                $transferMoney->target_user_id = $user->id;
                $transferMoney->transfer_money_value = $amount;
                $transferMoney->assigned_at = Carbon::now("Asia/Shanghai");
                if (!empty($message)) {
                    $transferMoney->notes = $message;
                }
                $transferMoney->save();
                $this->dispatcher->dispatch(new MoneyUpdated($user));
                $user->money += $amount;
                $user->save();
                $this->notifications->sync(new TransferMoneyBlueprint($transferMoney), [$user]);
            });
        }

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
