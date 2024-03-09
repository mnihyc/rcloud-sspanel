{include file='user/header.tpl'}

<script src="//{$config['jsdelivr_url']}/npm/jquery/dist/jquery.min.js"></script>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">链接您的 Telegram 账户</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">依据 RCLOUD 使用条款之第一小节，您必须绑定 Telegram 账户以作为 RCLOUD 账户的有效来源。</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        {if $user->im_type === 4}
                            <div class="mb-3">
                                <p class="lead">已与 Telegram 账户 @{htmlentities($user->im_username)} 进行绑定。</p>
                                <p class="lead">您可正常使用 RCLOUD 账户。</p>
                                <div class="d-flex">
                                    <button class="btn btn-red btn-md"
                                            hx-post="/user/unbind_im" hx-swap="none">
                                        解绑
                                    </button>
                                </div>
                            </div>
                        {else}
                            <div class="mb-3">
                                <p class="lead">要链接您的 Telegram 账户，首先添加我们的官方 bot <a target="view_window" href="https://t.me/{$public_setting['telegram_bot']}">@{$public_setting['telegram_bot']}</a>；</p>
                                <p class="lead">随后发送如下命令，请注意，该 token 仅用于绑定属于您名下的账户：</p>
                                <div id="link_command"></div>
                            </div>
                        {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let link_command = $('#link_command');
        
        function reloadLink() {
            $.ajax({
                type: "POST",
                url: "/user/must_link/token",
                dataType: "json",
                success: function (data) {
                    if (data.ret === 1) {
                        link_command.empty();
                        link_command.append('<a data-clipboard-text="/bind '+data.token+'" class="copy"><code>/bind '+data.token+'</code></a>（←单击复制）');
                        setTimeout(() => { reloadLink(); }, 5000);
                    } else {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                        link_command.empty();
                        link_command.append('<p><a href="#" onclick="window.location.reload()">刷新界面</a>以获取最新信息。</p>');
                    }
                }
            })
        }
    
        window.onload = function() {
            {if $user->im_type === 0} reloadLink(); {/if}
        };
    </script>

{include file='user/footer.tpl'}
