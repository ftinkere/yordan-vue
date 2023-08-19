<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('sounds')->insert([
            // Bilabial consonants
            ['sound' => 'm̥', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'm', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'p', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'b', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'ɸ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'β', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'p͡ɸ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'b͡β', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            // Labio-dental consonants
            ['sound' => 'ɱ̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɱ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            ['sound' => 'p̪', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'b̪', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            ['sound' => 'f', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'v', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            ['sound' => 'p̪͡f ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'b̪͡v', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            // Dental consonants
            ['sound' => 'n̪̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'n̪', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 't̪', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'd̪', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'θ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ð', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 't̪͡θ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'd̪͡ð', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'l̪̊', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'l̪', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'ɬ̪', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɮ̪', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'ɺ̪̊', 'table' => 'consonants', 'row' => 'Lateral flap', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɺ̪', 'table' => 'consonants', 'row' => 'Lateral flap', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            // Bilabial consonants
            ['sound' => 'β̞̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'β̞', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'ʙ̥', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʙ', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'ⱱ̟̊', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Bilabial', 'sub_column' => 'Voiceless'],
            ['sound' => 'ⱱ̟', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Bilabial', 'sub_column' => 'Voiced'],

            ['sound' => 'ʙ̥', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Bilabial', 'sub_column' =>  null],

            ['sound' => 'ʙ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Bilabial', 'sub_column' => null],

            ['sound' => 'pʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Bilabial', 'sub_column' => null],
            ['sound' => 'ɸʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Bilabial', 'sub_column' => null],

            // Labio-dental consonants
            ['sound' => 'ʋ̥', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʋ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            ['sound' => 'ⱱ̥', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Labio-dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ⱱ', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Labio-dental', 'sub_column' => 'Voiced'],

            ['sound' => 'fʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Labio-dental', 'sub_column' => null],

            // Dental consonants
            ['sound' => 'ð̞̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ð̞', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'r̪̊', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'r̪', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'ɾ̪̊', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Dental', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɾ̪', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Dental', 'sub_column' => 'Voiced'],

            ['sound' => 'ǀ', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Dental', 'sub_column' =>  null],

            ['sound' => 'ɗ̪', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Dental', 'sub_column' => null],

            ['sound' => 'θʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Dental', 'sub_column' => null],

            // Alveolar consonants
            ['sound' => 'n̥', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'n', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 't', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'd', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 's', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'z', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 't͡s', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'd͡z', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'l̥', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'l', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ɬ', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɮ', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ɺ̥', 'table' => 'consonants', 'row' => 'Lateral flap', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɺ', 'table' => 'consonants', 'row' => 'Lateral flap', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ɹ̥', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɹ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'r̥', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'r', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ɾ̥', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɾ', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ǃ', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Alveolar', 'sub_column' =>  null],

            ['sound' => 'ɗ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Alveolar', 'sub_column' => null],

            ['sound' => 'tʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Alveolar', 'sub_column' => null],
            ['sound' => 'sʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Alveolar', 'sub_column' => null],
            ['sound' => 'ɬʼ', 'table' => 'consonants', 'row' => 'Lateral fricative ejective', 'column' => 'Alveolar', 'sub_column' => null],

            // Post-alveolar consonants
            ['sound' => 'ʃ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Post-alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʒ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Post-alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 't͡ʃ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Post-alveolar', 'sub_column' => 'Voiceless'],
            ['sound' => 'd͡ʒ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Post-alveolar', 'sub_column' => 'Voiced'],

            ['sound' => 'ʃʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Post-alveolar', 'sub_column' => null],

            // Retroflex consonants
            ['sound' => 'ɳ̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɳ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ʈ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɖ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ʂ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʐ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ʈ͡ʂ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɖ͡ʐ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ɭ̊', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɭ', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ꞎ', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɭ̝', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ɻ̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɻ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => 'ɽ̊', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Retroflex', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɽ', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Retroflex', 'sub_column' => 'Voiced'],

            ['sound' => '‼', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Retroflex', 'sub_column' =>  null],

            ['sound' => 'ᶑ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Retroflex', 'sub_column' => null],

            ['sound' => 'ʈʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Retroflex', 'sub_column' => null],
            ['sound' => 'ʂʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Retroflex', 'sub_column' => null],

            // Alveolo-palatal consonants
            ['sound' => 'ȵ̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ȵ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ȶ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ȡ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ɕ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʑ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiced'],

            ['sound' => 't͡ɕ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'd͡ʑ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ȴ̊', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ȴ', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Alveolo-palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ǁ', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Alveolo-palatal', 'sub_column' =>  null],

            // Palatal consonants
            ['sound' => 'ɲ̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɲ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'c', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɟ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ç', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʝ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'c͡ç', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɟ͡ʝ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ʎ̥', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʎ', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ʎ̝̊', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʎ̝', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'j̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Palatal', 'sub_column' => 'Voiceless'],
            ['sound' => 'j', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Palatal', 'sub_column' => 'Voiced'],

            ['sound' => 'ǂ', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Palatal', 'sub_column' =>  null],

            ['sound' => 'ʄ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Palatal', 'sub_column' => null],

            ['sound' => 'cʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Palatal', 'sub_column' => null],
            ['sound' => 'ɕʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Palatal', 'sub_column' => null],

            // Labio-velar consonants
            ['sound' => 'ʍ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Labio-velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'w', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Labio-velar', 'sub_column' => 'Voiced'],

            // Velar consonants
            ['sound' => 'ŋ̊', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ŋ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'k', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'g', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'x', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɣ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'k͡x', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'g͡ɣ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'ʟ̥', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʟ', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'ʟ̝̊', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʟ̝', 'table' => 'consonants', 'row' => 'Lateral fricative', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'ɰ̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Velar', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɰ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Velar', 'sub_column' => 'Voiced'],

            ['sound' => 'ʞ', 'table' => 'consonants', 'row' => 'Click', 'column' => 'Velar', 'sub_column' =>  null],

            ['sound' => 'ɠ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Velar', 'sub_column' => null],

            ['sound' => 'kʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Velar', 'sub_column' => null],
            ['sound' => 'xʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Velar', 'sub_column' => null],

            // Uvular consonants
            ['sound' => 'ɴ̥', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɴ', 'table' => 'consonants', 'row' => 'Nasal', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'q', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɢ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'χ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʁ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'q͡χ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɢ͡ʁ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'ʁ̞̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʁ̞', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'ʀ̥', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʀ', 'table' => 'consonants', 'row' => 'Trill', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'q̆', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Uvular', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɢ̆', 'table' => 'consonants', 'row' => 'Flap', 'column' => 'Uvular', 'sub_column' => 'Voiced'],

            ['sound' => 'ʛ', 'table' => 'consonants', 'row' => 'Implosive', 'column' => 'Uvular', 'sub_column' => null],

            ['sound' => 'qʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Uvular', 'sub_column' => null],
            ['sound' => 'χʼ', 'table' => 'consonants', 'row' => 'Fricative ejective', 'column' => 'Uvular', 'sub_column' => null],

            // Pharyngeal consonants
            ['sound' => 'ħ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Pharyngeal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʕ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Pharyngeal', 'sub_column' => 'Voiced'],

            // Epiglottal consonants
            ['sound' => 'ʡ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Epiglottal', 'sub_column' => null],

            ['sound' => 'ʜ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Epiglottal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ʢ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Epiglottal', 'sub_column' => 'Voiced'],

            ['sound' => 'ʡʼ', 'table' => 'consonants', 'row' => 'Plosive ejective', 'column' => 'Epiglottal', 'sub_column' => null],

            // Glottal consonants
            ['sound' => 'ʔ', 'table' => 'consonants', 'row' => 'Plosive', 'column' => 'Glottal', 'sub_column' => null],

            ['sound' => 'h', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Glottal', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɦ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Glottal', 'sub_column' => 'Voiced'],

            // Other consonants
            ['sound' => 'ɧ', 'table' => 'consonants', 'row' => 'Fricative', 'column' => 'Other', 'sub_column' => null],

            ['sound' => 't͡ɬ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Other', 'sub_column' => 'Voiceless'],
            ['sound' => 'd͡ɮ', 'table' => 'consonants', 'row' => 'Affricate', 'column' => 'Other', 'sub_column' => 'Voiced'],

            ['sound' => 'ɫ̥', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Other', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɫ', 'table' => 'consonants', 'row' => 'Lateral approximant', 'column' => 'Other', 'sub_column' => 'Voiced'],

            ['sound' => 'ɥ̊', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Other', 'sub_column' => 'Voiceless'],
            ['sound' => 'ɥ', 'table' => 'consonants', 'row' => 'Approximant', 'column' => 'Other', 'sub_column' => 'Voiced'],


            // Front and Near-front vowels
            ['sound' => 'i', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Front', 'sub_column' => 'Unrounded'],
            ['sound' => 'y', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Front', 'sub_column' => 'Rounded'],

            ['sound' => 'ɪ', 'table' => 'vowels', 'row' => 'Near-close', 'column' => 'Near-front', 'sub_column' => 'Unrounded'],
            ['sound' => 'ʏ', 'table' => 'vowels', 'row' => 'Near-close', 'column' => 'Near-front', 'sub_column' => 'Rounded'],

            ['sound' => 'e', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Front', 'sub_column' => 'Unrounded'],
            ['sound' => 'ø', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Front', 'sub_column' => 'Rounded'],

            ['sound' => 'e̞', 'table' => 'vowels', 'row' => 'Mid', 'column' => 'Front', 'sub_column' => 'Unrounded'],
            ['sound' => 'ø̞', 'table' => 'vowels', 'row' => 'Mid', 'column' => 'Front', 'sub_column' => 'Rounded'],

            ['sound' => 'ɛ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Front', 'sub_column' => 'Unrounded'],
            ['sound' => 'œ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Front', 'sub_column' => 'Rounded'],

            ['sound' => 'æ', 'table' => 'vowels', 'row' => 'Near-open', 'column' => 'Front', 'sub_column' => 'Unrounded'],

            ['sound' => 'a', 'table' => 'vowels', 'row' => 'Open', 'column' => 'Front', 'sub_column' => 'Unrounded'],
            ['sound' => 'ɶ', 'table' => 'vowels', 'row' => 'Open', 'column' => 'Front', 'sub_column' => 'Rounded'],

            // Central vowels
            ['sound' => 'ɨ', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Central', 'sub_column' => 'Unrounded'],
            ['sound' => 'ʉ', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Central', 'sub_column' => 'Rounded'],

            ['sound' => 'ɪ̈', 'table' => 'vowels', 'row' => 'Near-close', 'column' => 'Central', 'sub_column' => 'Unrounded'],
            ['sound' => 'ʊ̈', 'table' => 'vowels', 'row' => 'Near-close', 'column' => 'Central', 'sub_column' => 'Rounded'],

            ['sound' => 'ɘ', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Central', 'sub_column' => 'Unrounded'],
            ['sound' => 'ɵ', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Central', 'sub_column' => 'Rounded'],

            ['sound' => 'ə', 'table' => 'vowels', 'row' => 'Mid', 'column' => 'Central', 'sub_column' => null],

            ['sound' => 'ɜ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Central', 'sub_column' => 'Unrounded'],
            ['sound' => 'ɞ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Central', 'sub_column' => 'Rounded'],

            ['sound' => 'ɐ', 'table' => 'vowels', 'row' => 'Near-open', 'column' => 'Central', 'sub_column' => 'Unrounded'],

            ['sound' => 'ä', 'table' => 'vowels', 'row' => 'Open', 'column' => 'Central', 'sub_column' => 'Unrounded'],

            // Back and Near-back vowels
            ['sound' => 'ʊ', 'table' => 'vowels', 'row' => 'Near-close', 'column' => 'Near-back', 'sub_column' => 'Rounded'],

            ['sound' => 'ɯ', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Back', 'sub_column' => 'Unrounded'],
            ['sound' => 'u', 'table' => 'vowels', 'row' => 'Close', 'column' => 'Back', 'sub_column' => 'Rounded'],

            ['sound' => 'ɤ', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Back', 'sub_column' => 'Unrounded'],
            ['sound' => 'o', 'table' => 'vowels', 'row' => 'Close-mid', 'column' => 'Back', 'sub_column' => 'Rounded'],

            ['sound' => 'ɤ̞', 'table' => 'vowels', 'row' => 'Mid', 'column' => 'Back', 'sub_column' => 'Unrounded'],
            ['sound' => 'o̞', 'table' => 'vowels', 'row' => 'Mid', 'column' => 'Back', 'sub_column' => 'Rounded'],

            ['sound' => 'ʌ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Back', 'sub_column' => 'Unrounded'],
            ['sound' => 'ɔ', 'table' => 'vowels', 'row' => 'Open-mid', 'column' => 'Back', 'sub_column' => 'Rounded'],

            ['sound' => 'ɑ', 'table' => 'vowels', 'row' => 'Open', 'column' => 'Back', 'sub_column' => 'Unrounded'],
            ['sound' => 'ɒ', 'table' => 'vowels', 'row' => 'Open', 'column' => 'Back', 'sub_column' => 'Rounded'],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sounds')->truncate();
    }
};
