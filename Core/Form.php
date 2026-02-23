<?php

declare(strict_types=1);

namespace App\Core;

final class Form
{
    private string $formCode = '';

    /**
     * Données utilisées pour re-remplir le formulaire (v2+).
     * Typiquement: new Form($_POST)
     */
    public function __construct(private array $data = []) {}

    public function startForm(string $action = '', string $method = 'POST', array $attributes = []): self
    {
        // ✅ action par défaut : URL courante (évite '#', plus stable)
        if ($action === '') {
            $action = $_SERVER['REQUEST_URI'] ?? '';
        }

        $this->formCode .= "\n<form action=\"" . self::escape($action) . "\" method=\"" . self::escape($method) . "\"";

        foreach ($attributes as $attribute => $value) {
            $this->formCode .= ' ' . self::escape((string) $attribute) . '="' . self::escape((string) $value) . '"';
        }

        $this->formCode .= '>';
        return $this;
    }

    public function endForm(): self
    {
        $this->formCode .= "\n</form>";
        return $this;
    }

    public function addLabel(string $for, string $text, array $attributes = []): self
    {
        $this->formCode .= "\n<label for=\"" . self::escape($for) . "\"";
        foreach ($attributes as $attribute => $value) {
            $this->formCode .= ' ' . self::escape((string) $attribute) . '="' . self::escape((string) $value) . '"';
        }
        $this->formCode .= '>' . self::escape($text) . '</label>';
        return $this;
    }

    public function addInput(string $type, string $name, array $attributes = []): self
    {
        // ✅ si pas d'id fourni, on met id=name (utile avec label for=)
        if (!isset($attributes['id']) && $type !== 'hidden') {
            $attributes['id'] = $name;
        }
        // v2+: re-remplissage (sauf file, submit, password)
        if (!isset($attributes['value']) && $this->shouldPrefill($type)) {
            $value = $this->data[$name] ?? null;
            if (is_scalar($value)) {
                $attributes['value'] = (string) $value;
            }
        }
        $this->formCode .= "\n<input type=\"" . self::escape($type) . "\" name=\"" . self::escape($name) . '"';
        foreach ($attributes as $attribute => $value) {
            $this->formCode .= ' ' . self::escape((string) $attribute) . '="' . self::escape((string) $value) . '"';
        }
        $this->formCode .= '>';
        return $this;
    }

    public function addTextarea(string $name, string $value = '', array $attributes = []): self
    {
        // ✅ id par défaut
        if (!isset($attributes['id'])) {
            $attributes['id'] = $name;
        }
        $this->formCode .= "\n<textarea name=\"" . self::escape($name) . '"';
        foreach ($attributes as $attribute => $val) {
            $this->formCode .= ' ' . self::escape((string) $attribute) . '="' . self::escape((string) $val) . '"';
        }
        $this->formCode .= '>';
        // v2+: re-remplissage si $_POST fourni
        $content = $this->data[$name] ?? $value;
        $this->formCode .= self::escape(is_scalar($content) ? (string) $content : '');
        $this->formCode .= '</textarea>';
        return $this;
    }

    public function addSelect(string $name, array $options, array $attributes = []): self
    {
        // ✅ id par défaut
        if (!isset($attributes['id'])) {
            $attributes['id'] = $name;
        }
        $this->formCode .= "\n<select name=\"" . self::escape($name) . '"';
        foreach ($attributes as $attribute => $val) {
            $this->formCode .= ' ' . self::escape((string) $attribute) . '="' . self::escape((string) $val) . '"';
        }
        $this->formCode .= '>';
        $selected = $this->data[$name] ?? null;
        foreach ($options as $value => $label) {
            $this->formCode .= "\n <option value=\"" . self::escape((string) $value) . '"';
            if ($selected !== null && (string) $selected === (string) $value) {
                $this->formCode .= ' selected';
            }
            $this->formCode .= '>' . self::escape((string) $label) . '</option>';
        }
        $this->formCode .= "\n</select>";
        return $this;
    }

    public function getFormElements(): string
    {
        return $this->formCode;
    }

    /* =======================
    * Validation helpers
    * ======================= */
    /**
     * Vérifie que les champs existent et ne sont pas vides.
     */
    public static function validatePost(array $post, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($post[$field])) {
                return false;
            }
            if (!is_string($post[$field])) {
                return false;
            }
            if (trim($post[$field]) === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Validation fichiers (upload + image).
     * - Si UPLOAD_ERR_NO_FILE => OK (champ optionnel), on continue.
     * - Vérifie aussi taille max et MIME réel.
     */
    public static function validateFiles(
        array $files,
        array $fields,
        int $maxBytes = 2_000_000,
        array $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
    ): bool {
        foreach ($fields as $field) {
            if (!isset($files[$field])) {
                return false;
            }

            $f = $files[$field];

            if (!isset($f['error'], $f['tmp_name'], $f['size'])) {
                return false;
            }

            // ✅ optionnel : pas de fichier envoyé => on passe au champ suivant
            if ((int) $f['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ((int) $f['error'] !== UPLOAD_ERR_OK) {
                return false;
            }

            $tmp = (string) $f['tmp_name'];
            if ($tmp === '' || !is_file($tmp)) {
                return false;
            }

            $size = (int) $f['size'];
            if ($size <= 0 || $size > $maxBytes) {
                return false;
            }

            // Vérifie que c'est une image (dimensions + type basique)
            if (getimagesize($tmp) === false) {
                return false;
            }

            // MIME réel (plus fiable que l'extension)
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp);
            if (!in_array($mime, $allowedMime, true)) {
                return false;
            }
        }

        return true;
    }

    /* =======================
    * Internal helpers
    * ======================= */
    private function shouldPrefill(string $type): bool
    {
        $type = strtolower($type);
        return !in_array($type, ['file', 'password', 'submit'], true);
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
