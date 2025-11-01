<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    private Collection $languages;
    private int $translations_per_language = 25000;
    private int $batch_size = 1000;

    public function run()
    {
        $this->command->info('Starting translation seeding...');

        // Get languages
        $this->languages = Language::get();

        if ($this->languages->isEmpty()) {
            $this->command->error('No languages found. Please run LanguageSeeder first.');
            return;
        }

        $start_time = microtime(true);

        // Create translation groups
        $this->command->info("Creating {$this->translations_per_language} translation groups...");

        $total_groups_created = 0;

        for ($i = 0; $i < $this->translations_per_language; $i += $this->batch_size) {
            $current_batch_size = min($this->batch_size, $this->translations_per_language - $i);

            $groups = TranslationGroup::factory()
                ->count($current_batch_size)
                ->create();

            $total_groups_created += $current_batch_size;
            $this->command->info("Created {$total_groups_created}/{$this->translations_per_language} groups");

            // Create translations for each language for these groups
            $this->createTranslationsForGroups($groups);
        }

        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 2);

        $this->command->info("Seeding completed in {$execution_time} seconds");
        $this->command->info("Total groups created: {$total_groups_created}");
        $this->command->info("Total translations created: " . ($total_groups_created * $this->languages->count()));
    }

    private function createTranslationsForGroups($groups): void
    {
        $translations_data = [];

        foreach ($groups as $group) {
            foreach ($this->languages as $language) {
                $translations_data[] = [
                    'translation_group_id' => $group->id,
                    'language_id' => $language->id,
                    'value' => $this->generateTranslationValue($group->key, $language->code),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert in batches to avoid memory issues
                if (count($translations_data) >= 500) {
                    DB::table('translations')->insert($translations_data);
                    $translations_data = [];
                }
            }
        }

        // Insert any remaining translations
        if (!empty($translations_data)) {
            DB::table('translations')->insert($translations_data);
        }
    }

    private function generateTranslationValue(string $key, string $language_code): string
    {
        $key_parts = explode('.', $key);
        $category = $key_parts[0] ?? 'common';

        $translations_by_language = [
            'en' => $this->getEnglishTranslations($category),
            'fr' => $this->getFrenchTranslations($category),
            'es' => $this->getSpanishTranslations($category),
            'de' => $this->getGermanTranslations($category),
        ];

        $available_translations = $translations_by_language[$language_code] ?? $translations_by_language['en'];

        return $available_translations[array_rand($available_translations)];
    }

    private function getEnglishTranslations(string $category): array
    {
        $base_translations = [
            'Save changes',
            'Cancel operation',
            'Loading...',
            'Success!',
            'Error occurred',
            'Please wait',
            'Yes',
            'No',
            'OK',
            'Submit',
            'Search',
            'Filter',
            'Sort by',
            'Welcome back',
            'Please sign in',
            'Enter your credentials',
            'Create account',
            'This field is required',
            'Invalid format',
            'Must be at least :min characters',
        ];

        return array_merge($base_translations, [
            $category . ' specific text',
            'Random ' . $category . ' content',
            ucfirst($category) . ' related message',
        ]);
    }

    private function getFrenchTranslations(string $category): array
    {
        $base_translations = [
            'Enregistrer les modifications',
            'Annuler l\'opération',
            'Chargement...',
            'Succès !',
            'Une erreur est survenue',
            'Veuillez patienter',
            'Oui',
            'Non',
            'OK',
            'Soumettre',
            'Rechercher',
            'Filtrer',
            'Trier par',
            'Bienvenue',
            'Veuillez vous connecter',
            'Entrez vos identifiants',
            'Créer un compte',
            'Ce champ est obligatoire',
            'Format invalide',
            'Doit contenir au moins :min caractères',
        ];

        return array_merge($base_translations, [
            'Texte spécifique ' . $category,
            'Contenu aléatoire ' . $category,
            'Message lié à ' . $category,
        ]);
    }

    private function getSpanishTranslations(string $category): array
    {
        $base_translations = [
            'Guardar cambios',
            'Cancelar operación',
            'Cargando...',
            '¡Éxito!',
            'Ocurrió un error',
            'Por favor espere',
            'Sí',
            'No',
            'OK',
            'Enviar',
            'Buscar',
            'Filtrar',
            'Ordenar por',
            'Bienvenido de nuevo',
            'Por favor inicie sesión',
            'Ingrese sus credenciales',
            'Crear cuenta',
            'Este campo es obligatorio',
            'Formato inválido',
            'Debe tener al menos :min caracteres',
        ];

        return array_merge($base_translations, [
            'Texto específico de ' . $category,
            'Contenido aleatorio de ' . $category,
            'Mensaje relacionado con ' . $category,
        ]);
    }

    private function getGermanTranslations(string $category): array
    {
        $base_translations = [
            'Änderungen speichern',
            'Vorgang abbrechen',
            'Lädt...',
            'Erfolg!',
            'Ein Fehler ist aufgetreten',
            'Bitte warten',
            'Ja',
            'Nein',
            'OK',
            'Absenden',
            'Suchen',
            'Filtern',
            'Sortieren nach',
            'Willkommen zurück',
            'Bitte anmelden',
            'Geben Sie Ihre Anmeldedaten ein',
            'Konto erstellen',
            'Dieses Feld ist erforderlich',
            'Ungültiges Format',
            'Muss mindestens :min Zeichen lang sein',
        ];

        return array_merge($base_translations, [
            $category . ' spezifischer Text',
            'Zufälliger ' . $category . ' Inhalt',
            ucfirst($category) . ' bezogene Nachricht',
        ]);
    }
}
