<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\TranslationGroup;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;
    protected $translation_group_key = null;

    public function definition()
    {
        $translation_templates = [
            "auth" => [
                "Welcome back, :name",
                "Please sign in to your account",
                "Enter your credentials",
                "Forgot your password?",
                "Create new account",
                "Logout successful",
            ],
            "validation" => [
                "The :field field is required",
                "Please enter a valid :field",
                ":field must be at least :min characters",
                ":field confirmation does not match",
                "Invalid :field format",
            ],
            "ui" => [
                "Save changes",
                "Cancel operation",
                "Loading...",
                "Success!",
                "Error occurred",
                "Please wait",
            ],
            "email" => [
                "Verify your email address",
                "Click the link below to verify",
                "Email verification required",
                "Your verification code is :code",
            ],
            "common" => [
                "Yes",
                "No",
                "OK",
                "Cancel",
                "Submit",
                "Search",
                "Filter",
                "Sort by",
            ]
        ];

        $key_parts = explode(".", $this->getKeyContext());
        $category = $key_parts[0] ?? "common";

        $template_pool = $translation_templates[$category] ?? $translation_templates["common"];
        $base_text = $this->faker->randomElement($template_pool);

        // Add some variations to make it look natural
        $variations = [
            $base_text,
            ucfirst($base_text),
            $base_text . ".",
            $base_text . "!",
            str_replace(":name", $this->faker->firstName(), $base_text),
            str_replace(":field", $this->faker->word(), $base_text),
            str_replace(":min", $this->faker->numberBetween(3, 10), $base_text),
        ];

        return [
            "translation_group_id" => TranslationGroup::factory(),
            "language_id" => Language::factory(),
            "value" => $this->faker->randomElement($variations),
        ];
    }

    private function getKeyContext()
    {
        // This will be set when creating translations for a specific group
        return $this->translation_group_key ?? "common.general";
    }

    public function forLanguage(Language $language)
    {
        return $this->state(function (array $attributes) use ($language) {
            return [
                "language_id" => $language->id,
            ];
        });
    }

    public function forGroup(TranslationGroup $group)
    {
        return $this->state(function (array $attributes) use ($group) {
            $this->translation_group_key = $group->key;
            return [
                "translation_group_id" => $group->id,
            ];
        });
    }

    public function withFrenchTranslation()
    {
        $french_translations = [
            "Bienvenue, :name",
            "Veuillez vous connecter à votre compte",
            "Entrez vos identifiants",
            "Mot de passe oublié ?",
            "Créer un nouveau compte",
            "Déconnexion réussie",
            "Le champ :field est obligatoire",
            "Veuillez saisir un :field valide",
            ":field doit contenir au moins :min caractères",
            "Enregistrer les modifications",
            "Annuler l\"opération",
            "Chargement...",
            "Succès !",
            "Une erreur est survenue",
            "Oui",
            "Non",
            "OK",
            "Annuler",
            "Soumettre",
            "Rechercher",
        ];

        return $this->state(function (array $attributes) use ($french_translations) {
            return [
                "value" => $this->faker->randomElement($french_translations),
            ];
        });
    }

    public function withSpanishTranslation()
    {
        $spanish_translations = [
            "Bienvenido de nuevo, :name",
            "Por favor inicie sesión en su cuenta",
            "Ingrese sus credenciales",
            "¿Olvidó su contraseña?",
            "Crear nueva cuenta",
            "Cierre de sesión exitoso",
            "El campo :field es obligatorio",
            "Por favor ingrese un :field válido",
            ":field debe tener al menos :min caracteres",
            "Guardar cambios",
            "Cancelar operación",
            "Cargando...",
            "¡Éxito!",
            "Ocurrió un error",
            "Sí",
            "No",
            "OK",
            "Cancelar",
            "Enviar",
            "Buscar",
        ];

        return $this->state(function (array $attributes) use ($spanish_translations) {
            return [
                "value" => $this->faker->randomElement($spanish_translations),
            ];
        });
    }

    public function withGermanTranslation()
    {
        $german_translations = [
            "Willkommen zurück, :name",
            "Bitte melden Sie sich in Ihrem Konto an",
            "Geben Sie Ihre Anmeldedaten ein",
            "Passwort vergessen?",
            "Neues Konto erstellen",
            "Erfolgreich abgemeldet",
            "Das Feld :field ist erforderlich",
            "Bitte geben Sie ein gültiges :field ein",
            ":field muss mindestens :min Zeichen lang sein",
            "Änderungen speichern",
            "Vorgang abbrechen",
            "Lädt...",
            "Erfolg!",
            "Ein Fehler ist aufgetreten",
            "Ja",
            "Nein",
            "OK",
            "Abbrechen",
            "Absenden",
            "Suchen",
        ];

        return $this->state(function (array $attributes) use ($german_translations) {
            return [
                "value" => $this->faker->randomElement($german_translations),
            ];
        });
    }
}
