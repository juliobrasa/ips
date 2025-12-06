import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Soltia Primary - Violet/Purple (from soltia.net #bb4c81)
                primary: {
                    50: '#FCE4EC',
                    100: '#F8BBD9',
                    200: '#F48DC4',
                    300: '#E968A8',
                    400: '#D75490',
                    500: '#BB4C81',
                    600: '#A84474',
                    700: '#923B65',
                    800: '#7C3256',
                    900: '#5C2340',
                },
                // Soltia Secondary - Rose/Red (from soltia.net #DE3D50)
                secondary: {
                    50: '#FFEBEE',
                    100: '#FFCDD2',
                    200: '#F9A8B0',
                    300: '#F2808E',
                    400: '#E85A6B',
                    500: '#DE3D50',
                    600: '#D32F42',
                    700: '#C62635',
                    800: '#B91C28',
                    900: '#A0101B',
                },
                // Success - Green
                success: {
                    50: '#E8F5E9',
                    100: '#C8E6C9',
                    200: '#A5D6A7',
                    300: '#81C784',
                    400: '#66BB6A',
                    500: '#4CAF50',
                    600: '#43A047',
                    700: '#388E3C',
                    800: '#2E7D32',
                    900: '#1B5E20',
                },
                // Warning - Amber
                warning: {
                    50: '#FFF8E1',
                    100: '#FFECB3',
                    200: '#FFE082',
                    300: '#FFD54F',
                    400: '#FFCA28',
                    500: '#FFC107',
                    600: '#FFB300',
                    700: '#FFA000',
                    800: '#FF8F00',
                    900: '#FF6F00',
                },
                // Danger - Red
                danger: {
                    50: '#FFEBEE',
                    100: '#FFCDD2',
                    200: '#EF9A9A',
                    300: '#E57373',
                    400: '#EF5350',
                    500: '#F44336',
                    600: '#E53935',
                    700: '#D32F2F',
                    800: '#C62828',
                    900: '#B71C1C',
                },
            },
            boxShadow: {
                'material-1': '0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24)',
                'material-2': '0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23)',
                'material-3': '0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23)',
                'material-4': '0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22)',
                'material-5': '0 19px 38px rgba(0,0,0,0.30), 0 15px 12px rgba(0,0,0,0.22)',
            },
        },
    },

    plugins: [forms],
};
