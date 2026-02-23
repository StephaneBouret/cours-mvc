<?php

declare(strict_types=1);

namespace App\Core;

final class Form
{
    private string $formElements = '';
    private array $data = [];           // valeurs pour "re-remplir" (souvent $_POST)
    private ?string $csrfTokenId = null; // id du token CSRF pour ce formulaire

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getFormElements(): string
    {
        return $this->formElements;
    }

    public function __toString(): string
    {
        return $this->formElements;
    }

    // ----------------------------
    // 1) ATTRIBUTS HTML
    // ----------------------------
    private function addAttributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            // Attributs booléens HTML (required, disabled, checked, multiple, etc.)
            if ($value === true) {
                $html .= ' ' . $key;
                continue;
            }

            // Permet de retirer un attribut en passant null
            if ($value === null || $value === false) {
                continue;
            }

            $escapedKey = htmlspecialchars((string)$key, ENT_QUOTES);
            $escapedVal = htmlspecialchars((string)$value, ENT_QUOTES);
            $html .= " {$escapedKey}=\"{$escapedVal}\"";
        }

        return $html;
    }

    private function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    // ----------------------------
    // 2) GESTION DES VALEURS (re-remplissage)
    // ----------------------------
    private function getValue(string $name, mixed $default = ''): string
    {
        // Exemple: $data = $_POST, on récupère la valeur du champ si elle existe
        $value = $this->data[$name] ?? $default;

        if (is_array($value)) {
            // utile pour multi-select / checkbox groups
            return '';
        }

        return (string) $value;
    }

    private function isChecked(string $name, string $expectedValue = '1'): bool
    {
        $value = $this->data[$name] ?? null;

        if (is_array($value)) {
            return in_array($expectedValue, $value, true);
        }

        return (string)$value === $expectedValue;
    }

    // ----------------------------
    // 3) CSRF "simple"
    // ----------------------------
    public function enableCsrf(string $tokenId = 'form'): self
    {
        $this->csrfTokenId = $tokenId;
        return $this;
    }

    private function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Session non démarrée. Ajoute session_start() dans public/index.php.');
        }
    }

    private function generateCsrfToken(string $tokenId): string
    {
        $this->ensureSessionStarted();

        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf'][$tokenId] = $token;

        return $token;
    }

    public static function isCsrfTokenValid(string $tokenId, ?string $submittedToken): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $stored = $_SESSION['_csrf'][$tokenId] ?? null;
        if (!is_string($stored) || !is_string($submittedToken)) {
            return false;
        }

        // comparaison sûre contre timing attacks
        return hash_equals($stored, $submittedToken);
    }

    private function addCsrfHiddenInput(): void
    {
        if ($this->csrfTokenId === null) {
            return;
        }

        $token = $this->generateCsrfToken($this->csrfTokenId);
        $this->formElements .= '<input type="hidden" name="_csrf" value="' . $this->esc($token) . '">';
    }

    // ----------------------------
    // 4) FORM TAG
    // ----------------------------
    public function startForm(string $action, string $method = 'POST', array $attributes = []): self
    {
        $actionEsc = $this->esc($action);
        $methodUp = strtoupper($method);
        $methodEsc = $this->esc($methodUp);

        $this->formElements .= "<form action=\"{$actionEsc}\" method=\"{$methodEsc}\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';

        // Si CSRF activé, on injecte le hidden dès le début du form
        $this->addCsrfHiddenInput();

        return $this;
    }

    public function endForm(): self
    {
        $this->formElements .= '</form>';
        return $this;
    }

    // ----------------------------
    // 5) CHAMPS
    // ----------------------------
    public function addLabel(string $for, string $text, array $attributes = []): self
    {
        $forEsc = $this->esc($for);
        $this->formElements .= "<label for=\"{$forEsc}\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';
        $this->formElements .= $this->esc($text) . '</label>';
        return $this;
    }

    public function addInput(string $type, string $name, array $attributes = []): self
    {
        $typeEsc = $this->esc($type);
        $nameEsc = $this->esc($name);

        // Remplissage auto : on met value=... pour les types compatibles
        $typesWithValue = ['text', 'email', 'date', 'number', 'search', 'tel', 'url', 'hidden'];
        if (in_array(strtolower($type), $typesWithValue, true) && !array_key_exists('value', $attributes)) {
            $attributes['value'] = $this->getValue($name);
        }

        // Checkbox/radio : checked si la valeur postée correspond
        if (in_array(strtolower($type), ['checkbox', 'radio'], true)) {
            $value = (string)($attributes['value'] ?? '1');
            if ($this->isChecked($name, $value)) {
                $attributes['checked'] = true;
            }
        }

        $this->formElements .= "<input type=\"{$typeEsc}\" name=\"{$nameEsc}\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';
        return $this;
    }

    public function addTextarea(string $name, string $text = '', array $attributes = []): self
    {
        $nameEsc = $this->esc($name);

        // si POST, on écrase le texte par la valeur postée
        $value = $this->getValue($name, $text);

        $this->formElements .= "<textarea name=\"{$nameEsc}\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';
        $this->formElements .= $this->esc($value) . '</textarea>';
        return $this;
    }

    /**
     * @param array $options Format simple : ['1' => 'Option A', '2' => 'Option B']
     * ou format avancé :
     * [
     *   ['value' => '1', 'label' => 'Option A', 'attributes' => ['data-x' => 'y']],
     * ]
     */
    public function addSelect(string $name, array $options, array $attributes = []): self
    {
        $nameEsc = $this->esc($name);
        $current = $this->getValue($name);

        $this->formElements .= "<select name=\"{$nameEsc}\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';

        foreach ($options as $key => $opt) {
            // Format avancé
            if (is_array($opt) && isset($opt['value'], $opt['label'])) {
                $value = (string)$opt['value'];
                $label = (string)$opt['label'];
                $optAttr = $opt['attributes'] ?? [];
            } else {
                // Format simple
                $value = (string)$key;
                $label = (string)$opt;
                $optAttr = [];
            }

            if ($current !== '' && $current === $value) {
                $optAttr['selected'] = true;
            }

            $this->formElements .= '<option value="' . $this->esc($value) . '"';
            $this->formElements .= $this->addAttributes($optAttr) . '>';
            $this->formElements .= $this->esc($label) . '</option>';
        }

        $this->formElements .= '</select>';
        return $this;
    }

    // ----------------------------
    // 6) VALIDATION SIMPLE
    // ----------------------------
    public static function validatePost(array $post, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($post[$field]) || trim((string)$post[$field]) === '') {
                return false;
            }
        }
        return true;
    }

    public static function validateFiles(array $files, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($files[$field]) || ($files[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                return false;
            }
        }
        return true;
    }
}
