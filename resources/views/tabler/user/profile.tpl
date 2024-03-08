{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">账户信息</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">浏览最近的登录和使用记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户邮箱</div>
                            </div>
                            <div class="h1 mb-3">{$user->email}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户昵称</div>
                            </div>
                            <div class="h1 mb-3">{$user->user_name}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户注册时间</div>
                            </div>
                            <div class="h1 mb-3">{$user->reg_date}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户累计使用流量</div>
                            </div>
                            <div class="h1 mb-3">{$user->totalTraffic()}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck my-3">
                <div class="col-md-6 com-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">最近10次成功登录记录</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter text-nowrap card-table">
                                <thead>
                                <tr>
                                    <th>IP</th>
                                    <th>时间</th>
                                    <th>IP归属地</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $logins as $login}
                                    <tr>
                                        <td>{$login->ip}</td>
                                        <td>{$login->datetime}</td>
                                        <td>{$login->location}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 com-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">当前在线IP</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter text-nowrap card-table">
                                <thead>
                                <tr>
                                    <th>IP</th>
                                    <th>IP归属地</th>
                                    <th>节点名称</th>
                                    <th>最后在线时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $ips as $ip}
                                    <tr>
                                        <td>{$ip->ip}</td>
                                        <td>{$ip->location}</td>
                                        <td>{$ip->node_name}</td>
                                        <td>{$ip->last_time}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{include file='user/footer.tpl'}
