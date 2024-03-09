<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Link;
use App\Models\Node;
use App\Services\Subscribe\Clash;
use App\Services\Subscribe\Json;
use App\Services\Subscribe\SingBox;
use App\Services\Subscribe\SIP002;
use App\Services\Subscribe\SIP008;
use App\Services\Subscribe\SS;
use App\Services\Subscribe\Trojan;
use App\Services\Subscribe\V2Ray;
use App\Services\Subscribe\V2RayJson;
use App\Services\Subscribe\Surfboard;
use App\Utils\Tools;
use Illuminate\Support\Collection;

final class Subscribe
{
    public static function getUniversalSubLink($user): string
    {
        $userid = $user->id;
        $token = (new Link())->where('userid', $userid)->first();

        if ($token === null) {
            $token = new Link();
            $token->userid = $userid;
            $token->token = Tools::genSubToken();
            $token->save();
        }

        return $_ENV['subUrl'] . '/sub/' . $token->token;
    }

    public static function getUserNodes($user, bool $show_all_nodes = false): Collection
    {
        $query = Node::query();
        $query->where('type', 1);

        if (! $show_all_nodes) {
            $query->where('node_class', '<=', $user->class);
        }

        if (! $user->is_admin) {
            $group = ($user->node_group !== 0 ? [0, $user->node_group] : [0]);
            $query->whereIn('node_group', $group);
        }

        $nodes = $query->where(static function ($query): void {
            $query->where('node_bandwidth_limit', '=', 0)->orWhereRaw('node_bandwidth < node_bandwidth_limit');
        })->orderBy('node_class')
            ->orderBy('name')
            ->get();
        
        // 导入 ext_info 子节点
        $nnodes = new Collection;
        foreach ($nodes as $node) {
            $nnodes[] = $node;
            $ext_info = json_decode($node->ext_info, true, JSON_UNESCAPED_SLASHES);
            if (count($ext_info) <= 0)
                continue;
            foreach ($ext_info as $snode) {
                if (!array_key_exists('push', $snode) || $snode['push'] !== 'yes')
                    continue;
                if($snode['level'] > $user->class)
                    continue;
                // 覆盖 $node
                $tnode = clone $node;
                $tnode->name = '['.$snode['name'].'] '.$tnode->name;
                //$tnode->node_class = $snode['level'];
                //$tnode->sort = $snode['type'];
                $tnode->server = $snode['server'];
                $tnode->custom_config = $snode['custom_config'] ?? $node['custom_config'];
                $tnode->ext_info = '[]';
                if (array_key_exists('custom_override', $snode)) {
                    // 传递 path
                    $cc = json_decode($tnode->custom_config, true, JSON_UNESCAPED_SLASHES);
                    foreach ($snode['custom_override'] as $key => $value) {
                        $cc[$key] = $value;
                    }
                    $tnode->custom_config = json_encode($cc, JSON_UNESCAPED_SLASHES);
                }
                $nnodes[] = $tnode;
            }
        }
        
        return $nnodes;
    }

    public static function getContent($user, $type): string
    {
        return self::getClient($type)->getContent($user);
    }

    public static function getClient($type): Json|SS|SIP002|V2Ray|Trojan|Clash|SIP008|SingBox|V2RayJson|Surfboard
    {
        return match ($type) {
            'ss' => new SS(),
            'sip002' => new SIP002(),
            'v2ray' => new V2Ray(),
            'trojan' => new Trojan(),
            'clash' => new Clash(),
            'sip008' => new SIP008(),
            'singbox' => new SingBox(),
            'v2rayjson' => new V2RayJson(),
            'surfboard' => new Surfboard(),
            default => new Json(),
        };
    }
}
