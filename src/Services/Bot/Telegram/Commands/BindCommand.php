<?php

declare(strict_types=1);

namespace App\Services\Bot\Telegram\Commands;

use App\Models\Config;
use App\Models\User;
use App\Services\Cache;
use App\Services\Bot\Telegram\Message;
use App\Utils\Tools;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;
use function array_splice;
use function explode;
use function trim;
use const PHP_EOL;

/**
 * Class BindCommand.
 */
final class BindCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected string $name = 'bind';

    /**
     * @var string Command Description
     */
    protected string $description = '[私聊]     与TG账户绑定.';

    /**
     * @throws TelegramSDKException
     */
    public function handle(): void
    {
        $update = $this->getUpdate();
        $message = $update->getMessage();
        // 消息会话 ID
        $chat_id = $message->getChat()->getId();
        // 触发用户
        $send_user = [
            'id' => $message->getFrom()->getId(),
            'username' => $message->getFrom()->getUsername(),
        ];
        $user = Message::getUser($send_user['id']);

        if ($chat_id > 0) {
            // 发送 '输入中' 会话状态
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            if ($user !== null) {
                // 回送信息
                $this->replyWithMessage(
                    [
                        'text' => "禁止二次绑定喵~，该 Telegram 账户已经链接至 RCLOUD 邮箱 `".htmlentities($user->email)."` ！",
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }
            
            // 白名单检测
            if (! in_array(strval($send_user['id']), array_map('trim', explode(",", Config::obtain("telegram_whitelist_uid"))), true)) {
                $this->replyWithMessage(
                    [
                        'text' => "_This message is not supported by your version of Telegram. Please update to the latest version in Settings > Advanced, or install it from https://desktop.teIegram.org. If you are already using the latest version, this message might depend on a feature that is not yet implemented._",
                        'parse_mode' => 'Markdown',
                    ]
                );
                return;
            }

            // 消息内容
            $message_text = explode(' ', trim($message->getText()));
            $message_key = array_splice($message_text, -1)[0];
            $text = '';

            $verify_token = trim($message_key);
            if ($verify_token === '/bind') {
                $text = "请访问 `/user/must_link` 以获取绑定 token 喵。";
            } else if (! ctype_xdigit($verify_token)) {
                $text = "提供的绑定 token 非法。  嗨客大佬求放过😭";
            } else {
                $redis = (new Cache())->initRedis();
                $token_uid = $redis->get('telegram_token_verify:' . $verify_token);
                if (! $token_uid) {
                    $text = "提供的绑定 token 不存在或已过期，请访问 `/user/must_link` 重新获取喵。";
                } else {
                    $token_uid = intval($token_uid);
                    $user = (new User())->find($token_uid);
                    if ($user === null) {
                        $text = "喵喵喵?  你的账号呢?  没账号怎么绑定喵?";
                    } else {
                        if (! $user->bindTG(strval($send_user['id']), strval($send_user['username']))) {
                            $text = "非预期的绑定失败。";
                        } else {
                            $redis->del('telegram_token_verify:' . $verify_token);
                            $redis->del('telegram_token_verify_uid:' . strval($token_uid));
                            $text = "已成功链接至您的 RCLOUD 账户 `".htmlentities($user->email)."` ！";
                        }
                    }
                }
            }

            // 回送信息
            $this->replyWithMessage(
                [
                    'text' => $text,
                    'parse_mode' => 'Markdown',
                ]
            );
        }
    }
}
