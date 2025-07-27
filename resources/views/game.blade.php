<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <link rel="stylesheet" href="https://unpkg.com/@sakun/system.css" />

    {{
        Vite::useHotFile(public_path('hot-front'))
            ->useBuildDirectory('assets/front')
            ->withEntryPoints(['resources/front/ts/app.ts'])
    }}

    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
