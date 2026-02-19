<?php

declare(strict_types=1);

namespace App\Core;

class Form
{
    private string $formElements = '';

    public function getFormElements(): string
    {
        return $this->formElements;
    }

    public function __toString()
    {
        return $this->formElements;
    }

    private function addAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            // attribut booléen (required, disabled…)
            if ($value === true) {
                $html .= " $key";
                continue;
            }
            $escaped = htmlspecialchars((string)$value, ENT_QUOTES);
            $html .= " $key=\"$escaped\"";
        }
        return $html;
    }

    public function startForm(
        string $action,
        string $method = 'POST',
        array $attributes = []
    ): self {
        $this->formElements .= "<form action=\"$action\" method=\"$method\"";
        $this->formElements .= $this->addAttributes($attributes) . '>';
        return $this;
    }

    public function addLabel(string $for, string $text, array $attributes = []): self
    {
        $this->formElements .= "<label for=\"$for\"";
        $this->formElements .= $this->addAttributes($attributes) . ">";
        $this->formElements .= htmlspecialchars($text) . "</label>";
        return $this;
    }

    public function addInput(string $type, string $name, array $attributes = []): self
    {
        $this->formElements .= "<input type=\"$type\" name=\"$name\"";
        $this->formElements .= $this->addAttributes($attributes) . ">";
        return $this;
    }

    public function addTextarea(
        string $name,
        string $text = '',
        array $attributes = []
    ): self {
        $this->formElements .= "<textarea name=\"$name\"";
        $this->formElements .= $this->addAttributes($attributes) . ">";
        $this->formElements .= htmlspecialchars($text) . "</textarea>";
        return $this;
    }

    public function endForm(): self
    {
        $this->formElements .= "</form>";
        return $this;
    }

    // ---------------- VALIDATION ----------------
    public static function validatePost(array $post, array $fields): bool
    {
        foreach ($fields as $field) {
            if (!isset($post[$field]) || trim($post[$field]) === '') {
                return false;
            }
        }
        return true;
    }

    public static function validateFiles(array $files, array $fields): bool
    {
        foreach ($fields as $field) {
            if (
                !isset($files[$field]) ||
                $files[$field]['error'] !== UPLOAD_ERR_OK
            ) {
                return false;
            }
        }
        return true;
    }
}
