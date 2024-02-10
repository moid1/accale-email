<script>
    @if (null !== Session::get('orig_admin_id') && Auth::user()->admin)
        notify({
            type: 'warning',
            message: `{!! trans('messages.current_login_as', ["name" => Auth::user()->customer->displayName()]) !!}<br>{!! trans('messages.click_to_return_to_origin_user', ["link" => action("Admin\AdminController@loginBack")]) !!}`,
            timeout: false,
        });
    @endif

    @if (
        \Auth::user()->customer &&
        \Auth::user()->customer->getOption("sending_server_option") == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN &&
        !\Auth::user()->customer->activeSendingServers()->count()
    )
        notify({
            type: 'warning',
            message: `{!! trans('messages.not_have_any_customer_sending_server', [
                'link' => action('SendingServerController@select'),
            ]) !!}`,
            timeout: false,
        });
    @endif

    @if (\Auth::user()->customer &&
        !\Auth::user()->customer->getCurrentActiveSubscription() &&
        !isset($subscriptionPage)
    )
        notify({
            type: 'warning',
            message: `{!! trans('messages.not_have_any_plan_notification', [
                'link' => action('SubscriptionController@index'),
            ]) !!}`,
            timeout: false,
        });
    @endif
</script>