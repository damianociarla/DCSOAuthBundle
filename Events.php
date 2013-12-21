<?php

namespace DCS\OAuthBundle;

class Events
{
    const BEFORE_UPDATE_EXISTING_USER   = 'dcs_oauth.event.before_update_existing_user';
    const BEFORE_CREATE_NEW_USER        = 'dcs_oauth.event.before_create_new_user';
    const AFTER_PERSIST_USER            = 'dcs_oauth.event.after_persist_user';

    const BEFORE_JOIN_OAUTH_INFO        = 'dcs_oauth.event.before_join_oauth_info';
    const AFTER_JOIN_OAUTH_INFO         = 'dcs_oauth.event.after_join_oauth_info';

    const BEFORE_UPDATE_LOGIN_PROVIDER  = 'dcs_oauth.event.before_update_login_provider';
    const AFTER_UPDATE_LOGIN_PROVIDER   = 'dcs_oauth.event.after_update_login_provider';

    const BEFORE_UPDATE_OAUTH_INFO      = 'dcs_oauth.event.before_update_oauth_info';
    const AFTER_UPDATE_OAUTH_INFO       = 'dcs_oauth.event.after_update_oauth_info';

    const BEFORE_SYNC_OAUTH_INFO        = 'dcs_oauth.event.before_sync_oauth_info';
    const AFTER_SYNC_OAUTH_INFO         = 'dcs_oauth.event.after_sync_oauth_info';
}