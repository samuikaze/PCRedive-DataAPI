<nav id="header" class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">璃乃學習筆記管理後台</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <template v-for="r in routes">
                <li v-if="!Array.isArray(r.route) && ((r.sysop && user.role_of === 1) || !r.sysop)" :key="r.id" :class="routeClass(r)">
                    <a class="nav-link"
                       v-bind:class="{disabled: r.disabled}"
                       :href="r.route" :onclick="(route == r.route) ? 'return false;' : 'return true;'"
                       :aria-disabled="(r.disabled) ? 'true' : ''"
                    >
                        @{{ r.name }} <span v-if="route == r.route" class="sr-only">(current)</span>
                    </a>
                </li>

                <li v-if="Array.isArray(r.route) && ((r.sysop && user.role_of === 1) || !r.sysop)" :key="r.id" class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @{{ r.name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a v-for="subroute in r.route"
                           :key="subroute.id"
                           class="dropdown-item"
                           v-bind:class="{disabled: subroute.disabled}"
                           :href="subroute.route"
                           :aria-disabled="(subroute.disabled) ? 'true' : ''"
                        >
                            @{{ subroute.name }}
                        </a>
                    </div>
                </li>
            </template>
        </ul>
        <span v-if="!Array.isArray(user)">
            <template v-if="loading">
                <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                    <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                </svg>&nbsp;
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;
                讀取中...
            </template>
            <template v-else>
                <span id="user-popover" class="pointer" tabindex="0" data-container="body" data-toggle="popover" data-placement="bottom" title="使用者選單">
                    <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
                        <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
                    </svg>&nbsp;
                    @{{ user.nickname }}
                    <span v-if="user.status != 0 && user.role_of == 2" class="text-secondary">(見習生)</span>
                    <span v-if="user.status == 0" class="text-secondary">(審核中)</span>
                </span>
                &nbsp;&nbsp;
                <a href="/" class="text-dark text-decoration-none">
                    <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-front" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm5 10v2a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2v5a2 2 0 0 1-2 2H5z"/>
                    </svg>&nbsp;&nbsp;
                    返回前台
                </a>
                &nbsp;&nbsp;
                <a href="/admin/logout" class="text-dark text-decoration-none" title="登出">
                    <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-lightning-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                    </svg>&nbsp;
                    登出
                </a>
            </template>
        </span>
        <span v-else>
            <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-question-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
            </svg>&nbsp;
            一般訪客
        </span>
        &nbsp;&nbsp;
        <a href="/" class="text-dark text-decoration-none">
            <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-front" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm5 10v2a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2v5a2 2 0 0 1-2 2H5z"/>
            </svg>&nbsp;&nbsp;
            返回前台
        </a>
    </div>

    <div class="modal fade" id="editUserData" tabindex="-1" aria-labelledby="editUserDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserDataLabel">編輯使用者資料</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body fixed-body">
                    <div class="row">
                        <div class="col-3">
                          <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">基本資料</a>
                            <a class="nav-link" id="v-pills-password-tab" data-toggle="pill" href="#v-pills-password" role="tab" aria-controls="v-pills-password" aria-selected="false">修改密碼</a>
                          </div>
                        </div>
                        <div class="col-9">
                          <div class="tab-content" id="v-pills-tabContent">
                            {{-- 修改資料 --}}
                            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                <div class="form-group row">
                                    <label for="staticUsername" class="col-sm-3 col-form-label">使用者名稱</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control-plaintext" id="staticUsername" :value="eUser.username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="e-nickname">暱稱</label>
                                    <input type="text" v-model="eUser.nickname" class="form-control" id="e-nickname" placeholder="請輸入新暱稱，未輸入會以使用者名稱當作暱稱">
                                </div>
                            </div>
                            {{-- 修改密碼 --}}
                            <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
                                <h5 class="text-danger text-center"><strong>請注意，變更密碼後會自動登出，如不變更密碼請留空</strong></h5>
                                <div class="form-group">
                                    <label for="e-password-orig">原密碼</label>
                                    <input type="password" v-model="eOrigPassword" class="form-control" id="e-password-orig" placeholder="請輸入原密碼">
                                </div>
                                <div class="form-group">
                                    <label for="e-password">修改密碼</label>
                                    <input type="password" v-model="ePassword" class="form-control" id="e-password" placeholder="請輸入新密碼">
                                </div>
                                <div class="form-group">
                                    <label for="e-pswd-conf">確認密碼</label>
                                    <input type="password" v-model="ePasswordConf" class="form-control" id="e-pswd-conf" placeholder="請再次輸入新密碼">
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                    <button type="button" class="btn btn-primary" v-on:click="fireEditProfile($event)">儲存</button>
                </div>
            </div>
        </div>
    </div>
</nav>
