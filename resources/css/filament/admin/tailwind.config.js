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
                    'sidebar': '#121832',
                    'topnav': '#06467d',
                    'banner': '#f2911b',
                    1: '#182138',
                    2: '#33436b',
                    3: '#2d3c61',
                },
            },
        },
    },
}
