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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // カスタムグラデーションカラー
                'gradient': {
                    'blue-purple': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'purple-pink': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                    'green-emerald': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                    'orange-red': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                },
                // ガラスモーフィズム用カラー
                'glass': {
                    'white': 'rgba(255, 255, 255, 0.1)',
                    'border': 'rgba(255, 255, 255, 0.2)',
                },
            },
            animation: {
                'gradient-shift': 'gradientShift 15s ease infinite',
                'float': 'float 6s ease-in-out infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'bounce-slow': 'bounce 2s infinite',
                'spin-slow': 'spin 3s linear infinite',
            },
            keyframes: {
                gradientShift: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
            },
            boxShadow: {
                '3xl': '0 35px 60px -12px rgba(0, 0, 0, 0.25)',
                '4xl': '0 50px 100px -20px rgba(0, 0, 0, 0.25)',
                'inner-lg': 'inset 0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                'glow': '0 0 20px rgba(59, 130, 246, 0.5)',
                'glow-lg': '0 0 40px rgba(59, 130, 246, 0.3)',
            },
            backdropBlur: {
                'xs': '2px',
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            transitionTimingFunction: {
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
            scale: {
                '102': '1.02',
                '103': '1.03',
                '105': '1.05',
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
        },
    },

    plugins: [forms],
};
