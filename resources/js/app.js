const feat = [
    [['ɓ', /ɓ/g], ['b_<', /b_</g], ['b_<', /b_</g], 'voiced bilabial implosive'],
    [['ɖ', /ɖ/g], ['d`', /d`/g], ['d`', /d`/g], 'voiced retroflex plosive'],
    [['ɗ', /ɗ/g], ['d_<', /d_</g], ['d_<', /d_</g], 'voiced alveolar implosive'],
    [['ɡ', /ɡ/g], ['g', /g/g], ['g', /g/g], 'voiced velar plosive'],
    [['ɠ', /ɠ/g], ['g_<', /g_</g], ['g_<', /g_</g], 'voiced velar implosive'],
    [['ɦ', /ɦ/g], ['h\\', /h\\/g], ['h\\', /h\\/g], 'voiced glottal fricative'],
    [['ʝ', /ʝ/g], ['j\\', /j\\/g], ['j\\', /j\\/g], 'voiced palatal fricative'],
    [['ɭ', /ɭ/g], ['l`', /l`/g], ['l`', /l`/g], 'retroflex lateral approximant'],
    [['ɺ', /ɺ/g], ['l\\', /l\\/g], ['l\\', /l\\/g], 'alveolar lateral flap'],
    [['ɳ', /ɳ/g], ['n`', /n`/g], ['n`', /n`/g], 'retroflex nasal'],
    [['ɸ', /ɸ/g], ['p\\', /p\\/g], ['p\\', /p\\/g], 'voiceless bilabial fricative'],
    [['ɽ', /ɽ/g], ['r`', /r`/g], ['r`', /r`/g], 'retroflex flap'],
    [['ɹ', /ɹ/g], ['r\\', /r\\/g], ['r\\', /r\\/g], 'alveolar approximant'],
    [['ɻ', /ɻ/g], ['r\\`', /r\\`/g], ['r\\`', /r\\`/g], 'retroflex approximant'],
    [['ʂ', /ʂ/g], ['s`', /s`/g], ['s`', /s`/g], 'voiceless retroflex fricative'],
    [['ɕ', /ɕ/g], ['s\\', /s\\/g], ['s\\', /s\\/g], 'voiceless alveolo-palatal fricative'],
    [['ʈ', /ʈ/g], ['t`', /t`/g], ['t`', /t`/g], 'voiceless retroflex plosive'],
    [['ʋ', /ʋ/g], ['v\\', /v\\/g], ['v\\', /v\\/g], 'labiodental approximant'],
    [['ɧ', /ɧ/g], ['x\\', /x\\/g], ['x\\', /x\\/g], 'voiceless palatal-velar fricative'],
    [['ʐ', /ʐ/g], ['z`', /z`/g], ['z`', /z`/g], 'voiced retroflex fricative'],
    [['ʑ', /ʑ/g], ['z\\', /z\\/g], ['z\\', /z\\/g], 'voiced alveolo-palatal fricative'],
    [['ʙ', /ʙ/g], ['B\\', /B\\/g], ['B\\', /B\\/g], 'bilabial trill'],
    [['ɢ', /ɢ/g], ['G\\', /G\\/g], ['G\\', /G\\/g], 'voiced uvular plosive'],
    [['ʛ', /ʛ/g], ['G\\_<', /G\\_</g], ['G\\_<', /G\\_</g], 'voiced uvular implosive'],
    [['ʜ', /ʜ/g], ['H\\', /H\\/g], ['H\\', /H\\/g], 'voiceless epiglottal fricative'],
    [['ɨ̞', /ɨ̞/g], ['I\\', /I\\/g], ['I\\', /I\\/g], 'near-close central unrounded vowel'],
    [['ɪ̈', /ɪ̈/g], ['I\\', /I\\/g], ['I\\', /I\\/g], 'near-close central unrounded vowel'],
    [['ɟ', /ɟ/g], ['J\\', /J\\/g], ['J\\', /J\\/g], 'voiced palatal plosive'],
    [['ʄ', /ʄ/g], ['J\\_<', /J\\_</g], ['J\\_<', /J\\_</g], 'voiced palatal implosive'],
    [['ɮ', /ɮ/g], ['K\\', /K\\/g], ['K\\', /K\\/g], 'voiced alveolar lateral fricative'],
    [['ʟ', /ʟ/g], ['L\\', /L\\/g], ['L\\', /L\\/g], 'velar lateral approximant'],
    [['ɰ', /ɰ/g], ['M\\', /M\\/g], ['M\\', /M\\/g], 'velar approximant'],
    [['ɴ', /ɴ/g], ['N\\', /N\\/g], ['N\\', /N\\/g], 'uvular nasal'],
    [['ʘ', /ʘ/g], ['O\\', /O\\/g], ['O\\', /O\\/g], 'bilabial click'],
    [['ʀ', /ʀ/g], ['R\\', /R\\/g], ['R\\', /R\\/g], 'uvular trill'],
    [['ʊ̈', /ʊ̈/g], ['U\\', /U\\/g], ['U\\', /U\\/g], 'near-close central rounded vowel'],
    [['ʉ̞', /ʉ̞/g], ['U\\', /U\\/g], ['U\\', /U\\/g], 'near-close central rounded vowel'],
    [['ħ', /ħ/g], ['X\\', /X\\/g], ['X\\', /X\\/g], 'voiceless pharyngeal fricative'],
    [['ˑ', /ˑ/g], [':\\', /:\\/g], [':\\', /:\\/g], 'half long'],
    [['ɘ', /ɘ/g], ['@\\', /@\\/g], ['@\\', /@\\/g], 'close-mid central unrounded vowel'],
    [['ɞ', /ɞ/g], ['3\\', /3\\/g], ['3\\', /3\\/g], 'open-mid central rounded vowel'],
    [['ɶ', /ɶ/g], ['&', /&/g], ['&\\', /&\\/g], 'open front rounded vowel'],
    [['ʕ', /ʕ/g], ['?\\', /\?\\/g], ['?\\', /\?\\/g], 'voiced pharyngeal fricative'],
    [['ʢ', /ʢ/g], ['<\\', /<\\/g], ['<\\', /<\\/g], 'voiced epiglottal fricative'],
//[['', //g], ['>', />/g], ['>', />/g], 'end nonsegmental notation'],
    [['ʡ', /ʡ/g], ['>\\', />\\/g], ['>\\', />\\/g], 'epiglottal plosive'],
//[['', //g], ['<', /</g], ['<', /</g], 'begin nonsegmental notation (e.g., SAMPROSA)'],
    [['ǃ', /ǃ/g], ['!\\', /!\\/g], ['!\\', /!\\/g], 'postalveolar click'],
    [['ꜜ', /ꜜ/g], ['!', /!/g], ['!', /!/g], 'downstep'],
    [['ǁ', /ǁ/g], ['|\\|\\', /\|\\\|\\/g], ['|\\|\\', /\|\\\|\\/g], 'alveolar lateral click'],
    [['ǀ', /\ǀ/g], ['|\\', /\|\\/g], ['|\\', /\|\\/g], 'dental click'],
    [['‖', /‖/g], ['||', /\|\|/g], ['||', /\|\|/g], 'major intonation group'],
    [['ǂ', /ǂ/g], ['=\\', /=\\/g], ['=\\', /=\\/g], 'palatal click'],
    [['‿', /‿/g], ['-\\', /-\\/g], ['-\\', /-\\/g], 'linking mark'],
    [['̈', /̈/g], ['_"', /_"/g], ['_"', /_"/g], 'centralized'],
    [['̟', /̟/g], ['_+', /_\+/g], ['_+', /_\+/g], 'advanced'],
    [['̠', /̠/g], ['_-', /_-/g], ['_-', /_-/g], 'retracted'],
    [['̌', /̌/g], ['_/', /_\//g], ['_/', /_\//g], 'rising tone'],
    [['̥', /̥/g], ['_0', /_0/g], ['_0', /_0/g], 'voiceless'],
    [['ʼ', /ʼ/g], ['_>', /_>/g], ['_>', /_>/g], 'ejective'],
    [['ˤ', /ˤ/g], ['_?\\', /_\?\\/g], ['_?\\', /_\?\\/g], 'pharyngealized'],
    [['̂', /̂/g], ['_\\', /_\\/g], ['_\\', /_\\/g], 'falling tone'],
    [['̯', /̯/g], ['_^', /_^/g], ['_^', /_^/g], 'non-syllabic'],
    [['̚', /̚/g], ['_}', /_}/g], ['_}', /_}/g], 'no audible release'],
    [['˞', /˞/g], ['`', /`/g], ['`', /`/g], 'rhotacized'],
    [['̃', /̃/g], ['~', /_~/g], ['~', /_~/g], 'nasalized'],
    [['̃', /̃/g], ['~', /~/g], ['~', /~/g], 'nasalized'],
    [['̃', /̃/g], ['_~', /_~/g], ['_~', /_~/g], 'nasalized'],
    [['̘', /̘/g], ['_A', /_A/g], ['_A', /_A/g], 'advanced tongue root'],
    [['̺', /̺/g], ['_a', /_a/g], ['_a', /_a/g], 'apical'],
    [[' ᷅', / ᷅/g], ['_B_L', /_B_L/g], ['_B_L', /_B_L/g], 'low rising tone'],
    [['̏', /̏/g], ['_B', /_B/g], ['_B', /_B/g], 'extra low tone'],
    [['̜', /̜/g], ['_c', /_c/g], ['_c', /_c/g], 'less rounded'],
    [['̪', /̪/g], ['_d', /_d/g], ['_d', /_d/g], 'dental'],
    [['̴', /̴/g], ['_e', /_e/g], ['_e', /_e/g], 'velarized or pharyngealized; also see 5'],
    [['↘', /↘/g], ['<F>', /<F>/g], ['<F>', /<F>/g], 'global fall'],
    [['ˠ', /ˠ/g], ['_G', /_G/g], ['_G', /_G/g], 'velarized'],
    [[' ᷄', / ᷄/g], ['_H_T', /_H_T/g], ['_H_T', /_H_T/g], 'high rising tone'],
    [['́', /́/g], ['_H', /_H/g], ['_H', /_H/g], 'high tone'],
    [['ʰ', /ʰ/g], ['_h', /_h/g], ['_h', /_h/g], 'aspirated'],
    [['̰', /̰/g], ['_k', /_k/g], ['_k', /_k/g], 'creaky voice'],
    [['̀', /̀/g], ['_L', /_L/g], ['_L', /_L/g], 'low tone'],
    [['ˡ', /ˡ/g], ['_l', /_l/g], ['_l', /_l/g], 'lateral release'],
    [['̄', /̄/g], ['_M', /_M/g], ['_M', /_M/g], 'mid tone'],
    [['̻', /̻/g], ['_m', /_m/g], ['_m', /_m/g], 'laminal'],
    [['̼', /̼/g], ['_N', /_N/g], ['_N', /_N/g], 'linguolabial'],
    [['ⁿ', /ⁿ/g], ['_n', /_n/g], ['_n', /_n/g], 'nasal release'],
    [['̹', /̹/g], ['_O', /_O/g], ['_O', /_O/g], 'more rounded'],
    [['̞', /̞/g], ['_o', /_o/g], ['_o', /_o/g], 'lowered'],
    [['̙', /̙/g], ['_q', /_q/g], ['_q', /_q/g], 'retracted tongue root'],
    [['↗', /↗/g], ['<R>', /<R>/g], ['<R>', /<R>/g], 'global rise'],
    [[' ᷈', / ᷈/g], ['_R_F', /_R_F/g], ['_R_F', /_R_F/g], 'rising falling tone'],
    [['̌', /̌/g], ['_R', /_R/g], ['_R', /_R/g], 'rising tone'],
    [['̂', /̂/g], ['_F', /_F/g], ['_F', /_F/g], 'falling tone'],
    [['̝', /̝/g], ['_r', /_r/g], ['_r', /_r/g], 'raised'],
    [['̋', /̋/g], ['_T', /_T/g], ['_T', /_T/g], 'extra high tone'],
    [['̤', /̤/g], ['_t', /_t/g], ['_t', /_t/g], 'breathy voice'],
    [['̬', /̬/g], ['_v', /_v/g], ['_v', /_v/g], 'voiced'],
    [['ʷ', /ʷ/g], ['_w', /_w/g], ['_w', /_w/g], 'labialized'],
    [['̆', /̆/g], ['_X', /_X/g], ['_X', /_X/g], 'extra-short'],
    [['̽', /̽/g], ['_x', /_x/g], ['_x', /_x/g], 'mid-centralized'],
    [['ꜛ', /ꜛ/g], ['^', /\^/g], ['^', /\^/g], 'upstep'],
//[[' ', / /g], ['-', /-/g], ['-', /-/g], 'separator'],
    [['ː', /ː/g], [':', /:/g], [':', /:/g], 'long'],
    [['|', /\|/g], ['|', /\|/g], ['|', /\|/g], 'minor (foot) group'],
//[['', //g], ['/', ///g], ['/', ///g], 'indeterminacy in French vowels'],
    [['̩', /̩/g], ['=', /_=/g], ['=', /_=/g], 'syllabic'],
    [['̩', /̩/g], ['=', /=/g], ['=', /=/g], 'syllabic'],
    [['ˈ', /ˈ/g], ['"', /"/g], ["'", /'/g], 'primary stress'],
    [['ˌ', /ˌ/g], ['%', /%/g], [',', /,/g], 'secondary stress'],
    [['ə', /ə/g], ['@', /@/g], ['@', /@/g], 'schwa'],
    [['ʲ', /ʲ/g], ['_j', /_j/g], ['_j', /_j/g], 'palatalized'],
    [['æ', /æ/g], ['{', /\{/g], ['&', /&/g], 'near-open front unrounded vowel'],
    [['ʉ', /ʉ/g], ['}', /\}/g], ['u\\', /u\\/g], 'close central rounded vowel'],
    [['ʔ', /ʔ/g], ['?', /\?/g], ['?', /\?/g], 'glottal stop'],
    [['ɨ', /ɨ/g], ['1', /1/g], ['1', /1/g], 'close central unrounded vowel'],
    [['ø', /ø/g], ['2', /2/g], ['2', /2/g], 'close-mid front rounded vowel'],
    [['ɜ', /ɜ/g], ['3', /3/g], ['3', /3/g], 'open-mid central unrounded vowel'],
    [['ɾ', /ɾ/g], ['4', /4/g], ['4', /4/g], 'alveolar flap'],
    [['ɫ', /ɫ/g], ['5', /5/g], ['5', /5/g], 'velarized alveolar lateral approximant; also see _e'],
    [['ɐ', /ɐ/g], ['6', /6/g], ['6', /6/g], 'near-open central vowel'],
    [['ɤ', /ɤ/g], ['7', /7/g], ['7', /7/g], 'close-mid back unrounded vowel'],
    [['ɵ', /ɵ/g], ['8', /8/g], ['8', /8/g], 'close-mid central rounded vowel'],
    [['œ', /œ/g], ['9', /9/g], ['9', /9/g], 'open-mid front rounded vowel'],
    [['ɑ', /ɑ/g], ['A', /A/g], ['A', /A/g], 'open back unrounded vowel'],
    [['β', /β/g], ['B', /B/g], ['B', /B/g], 'voiced bilabial fricative'],
    [['ç', /ç/g], ['C', /C/g], ['C', /C/g], 'voiceless palatal fricative'],
    [['ð', /ð/g], ['D', /D/g], ['D', /D/g], 'voiced dental fricative'],
    [['ɛ', /ɛ/g], ['E', /E/g], ['E', /E/g], 'open-mid front unrounded vowel'],
    [['ɱ', /ɱ/g], ['F', /F/g], ['F', /F/g], 'labiodental nasal'],
    [['ɣ', /ɣ/g], ['G', /G/g], ['G', /G/g], 'voiced velar fricative'],
    [['ɥ', /ɥ/g], ['H', /H/g], ['H', /H/g], 'labial-palatal approximant'],
    [['ɪ', /ɪ/g], ['I', /I/g], ['I', /I/g], 'near-close front unrounded vowel'],
    [['ɲ', /ɲ/g], ['J', /J/g], ['J', /J/g], 'palatal nasal'],
    [['ɬ', /ɬ/g], ['K', /K/g], ['K', /K/g], 'voiceless alveolar lateral fricative'],
    [['ʎ', /ʎ/g], ['L', /L/g], ['L', /L/g], 'palatal lateral approximant'],
    [['ɯ', /ɯ/g], ['M', /M/g], ['M', /M/g], 'close back unrounded vowel'],
    [['ŋ', /ŋ/g], ['N', /N/g], ['N', /N/g], 'velar nasal'],
    [['ɔ', /ɔ/g], ['O', /O/g], ['O', /O/g], 'open-mid back rounded vowel'],
    [['ɒ', /ɒ/g], ['Q', /Q/g], ['Q', /Q/g], 'open back rounded vowel'],
    [['ʁ', /ʁ/g], ['R', /R/g], ['R', /R/g], 'voiced uvular fricative'],
    [['ʃ', /ʃ/g], ['S', /S/g], ['S', /S/g], 'voiceless postalveolar fricative'],
    [['θ', /θ/g], ['T', /T/g], ['T', /T/g], 'voiceless dental fricative'],
    [['ʊ', /ʊ/g], ['U', /U/g], ['U', /U/g], 'near-close back rounded vowel'],
    [['ʌ', /ʌ/g], ['V', /V/g], ['V', /V/g], 'open-mid back unrounded vowel'],
    [['ʍ', /ʍ/g], ['W', /W/g], ['W', /W/g], 'voiceless labial-velar fricative'],
    [['χ', /χ/g], ['X', /X/g], ['X', /X/g], 'voiceless uvular fricative'],
    [['ʏ', /ʏ/g], ['Y', /Y/g], ['Y', /Y/g], 'near-close front rounded vowel'],
    [['ʒ', /ʒ/g], ['Z', /Z/g], ['Z', /Z/g], 'voiced postalveolar fricative'],
    [['ʋ', /ʋ/g], ['P', /P/g], ['P', /P/g], 'labiodental approximant'],
    [['͡', /͡/g], ['_', /_/g], [')', /\)/g], 'affricate tie bar'], //t͡s
];

function x2i(text) {

    let new1 = text;

    for (let i = 0; i < feat.length; i++) {
        let pat = feat[i][1][1];
        new1 = new1.replace(pat, feat[i][0][0]);
    }

    return new1;
}
window.x2i = x2i;


import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

/* global Ziggy */

const appName = 'Ёрдан';

createInertiaApp({
    title: (title) => {
            return title ? `${title} - ${appName}` : `${appName}`;
        },
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

document.querySelector('[data-page]').attributes.removeNamedItem('data-page');
