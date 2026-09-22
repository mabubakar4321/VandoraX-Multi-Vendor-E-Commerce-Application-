/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                forest: {
                    DEFAULT: '#0E2A22',
                    900: '#081813',
                    700: '#14392E',
                    500: '#1E6F5C',
                    100: '#D6E3DD',
                },
                marigold: {
                    DEFAULT: '#F2B705',
                    600: '#D9A404',
                    100: '#FDF3D4',
                },
                vermilion: {
                    DEFAULT: '#B23A2E',
                    100: '#F7E4E1',
                },
                paper: '#F5F7F4',
                mist: '#E4EAE5',
                graphite: '#4A5551',
            },
            fontFamily: {
                display: ['"Bricolage Grotesque"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                sans: ['"Instrument Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            maxWidth: {
                prose: '62ch',
            },
        },
    },
    plugins: [],
};