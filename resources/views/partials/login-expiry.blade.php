@once
    @if (auth()->check())
        <script>
            (() => {
                const loginUrl = @json(route('login'));
                const statusUrl = @json(route('login.session-status'));

                let busy = false;
                let leaving = false;

                const leave = () => {
                    if (leaving) return;

                    leaving = true;
                    window.location.replace(loginUrl);
                };

                const check = async () => {
                    if (busy || leaving) return;

                    busy = true;

                    try {
                        const response = await fetch(statusUrl, {
                            credentials: 'same-origin',
                            cache: 'no-store',
                            headers: {
                                Accept: 'application/json',
                            },
                        });

                        if (
                            response.status === 401
                            || response.status === 419
                        ) {
                            leave();
                            return;
                        }

                        if (!response.ok) return;

                        const status = await response.json();

                        if (!status.authenticated) {
                            leave();
                        }
                    } catch (_) {
                        // 通信失敗の場合は、次の確認で再試行する。
                    } finally {
                        busy = false;
                    }
                };

                const installLivewireHook = () => {
                    window.Livewire.hook('request', ({ fail }) => {
                        fail(({ status, preventDefault }) => {
                            if (status === 401 || status === 419) {
                                preventDefault();
                                leave();
                            }
                        });
                    });
                };

                if (window.Livewire) {
                    installLivewireHook();
                } else {
                    document.addEventListener(
                        'livewire:init',
                        installLivewireHook,
                        { once: true }
                    );
                }

                check();

                const interval = window.setInterval(check, 30000);

                const onFocus = () => check();

                const onVisible = () => {
                    if (!document.hidden) {
                        check();
                    }
                };

                window.addEventListener('focus', onFocus);

                document.addEventListener(
                    'visibilitychange',
                    onVisible
                );

                document.addEventListener(
                    'livewire:navigating',
                    () => {
                        window.clearInterval(interval);
                        window.removeEventListener('focus', onFocus);

                        document.removeEventListener(
                            'visibilitychange',
                            onVisible
                        );
                    },
                    { once: true }
                );
            })();
        </script>
    @endif
@endonce