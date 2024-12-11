import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'custom-color': {
                    'sidebar': '#166534',
                    'topnav': '#15803d',
                    'banner': '#f2911b',
                    1: '#ffff',
                    2: '#ffff',
                    3: '#ffff',
                },
            },
        },
    },
}
