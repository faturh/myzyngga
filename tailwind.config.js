import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);

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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit', ...defaultTheme.fontFamily.sans],
                'dm-sans': ['DM Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                zyngga: {
                    blue: {
                        50: '#e8eff9',
                        300: '#1660C1',
                        400: '#0F4387',
                        500: '#0D3B76',
                    },
                    yellow: {
                        50: '#FEF4E9',
                        300: '#F7931E',
                        400: '#AD6715',
                        500: '#975A12',
                    },
                    neutral: {
                        100: '#FFFFFF',
                        200: '#F4F4F4',
                        300: '#CCCCCC',
                        400: '#808080',
                        500: '#0F0F0F',
                    },
                    status: {
                        success: '#21b557',
                        warning: '#f2af00',
                        error: '#ec0f04',
                        info: '#2563eb',
                        danger: '#ec0f04',
                    }
                }
            },
            animation: {
                'gradient-x': 'gradient-x 5s ease infinite',
            },
            keyframes: {
                'gradient-x': {
                    '0%, 100%': {
                        'background-size': '200% 200%',
                        'background-position': 'left center'
                    },
                    '50%': {
                        'background-size': '200% 200%',
                        'background-position': 'right center'
                    },
                },
            }
        },
    },

    plugins: [forms],
};
