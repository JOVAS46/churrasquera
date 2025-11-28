<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Churrascuteria Roberto') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>

    <script type="module">
        const { createApp, h } = Vue;
        const { createInertiaApp } = InertiaVue3;

        // Import components
        const modules = {
            Dashboard: () => import('/js/components/Dashboard.js')
        };

        createInertiaApp({
            resolve: async name => {
                if (modules[name]) {
                    const component = await modules[name]();
                    return component.default;
                }
                return { template: '<div>Component not found</div>' };
            },
            setup({ el, App, props, plugin }) {
                createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .mount(el);
            },
        });
    </script>
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
