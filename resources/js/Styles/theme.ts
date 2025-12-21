import Aura from '@primeuix/themes/aura';
import { definePreset } from '@primeuix/themes';

const InterdiscountPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '#fef3e2',
            100: '#fde4b8',
            200: '#fbd38d',
            300: '#f9c163',
            400: '#f7b03c',
            500: '#f5a623',
            600: '#e8951a',
            700: '#d4830f',
            800: '#b86e0a',
            900: '#8f5506',
            950: '#5c3803',
        },
    },
});

export default InterdiscountPreset;
