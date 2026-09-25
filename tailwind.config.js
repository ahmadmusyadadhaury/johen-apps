import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    50: '#eff7ff',
                    100: '#dbeefe',
                    200: '#bfdffe',
                    300: '#93c9fd',
                    400: '#60aafa',
                    500: '#0987F5',
                    600: '#036bd1',
                    700: '#0454a8',
                    800: '#084686',
                    900: '#0c3a6f',
                    950: '#08254a',
                },
                violet: {
                    50: '#f3f0ff',
                    100: '#e8e0ff',
                    200: '#d1c0fe',
                    300: '#b495fd',
                    400: '#9665f7',
                    500: '#854DEA',
                    600: '#7332d6',
                    700: '#6027b5',
                    800: '#4f2294',
                    900: '#421d79',
                    950: '#281154',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
                'float': 'float 6s ease-in-out infinite',
                'gradient-x': 'gradientX 8s ease infinite',
                'drift-slow': 'driftSlow 20s ease-in-out infinite',
                'tick': 'tick 0.15s ease-out',
                'shimmer': 'shimmer 2s ease-in-out infinite',
                'particle-drift': 'particleDrift 20s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(9, 135, 245, 0.2)' },
                    '50%': { boxShadow: '0 0 40px rgba(9, 135, 245, 0.4)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                gradientX: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                tick: {
                    '0%': { transform: 'translateY(-2px)', opacity: '0.7' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
                particleDrift: {
                    '0%, 100%': { transform: 'translate(0, 0) rotate(0deg)' },
                    '25%': { transform: 'translate(40px, -30px) rotate(3deg)' },
                    '50%': { transform: 'translate(-20px, -60px) rotate(-2deg)' },
                    '75%': { transform: 'translate(30px, -20px) rotate(4deg)' },
                },
                driftSlow: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '33%': { transform: 'translate(30px, -30px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                },
            },
        },
    },

    plugins: [forms],
};
