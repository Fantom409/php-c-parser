<?php

declare(strict_types=1);

namespace PHPCParser;

class Error extends \RuntimeException
{
    protected $rawMessage;
    protected $attributes;
    /**
     * Creates an Exception signifying a parse error.
     *
     * @param string    $message    Error message
     * @param array|int $attributes Attributes of node/token where error occurred
     *                              (or start line of error -- deprecated)
     */
    public function __construct(string $message, $attributes = []) {
        $this->rawMessage = $message;
        if (is_array($attributes)) {
            $this->attributes = $attributes;
        } else {
            $this->attributes = ['startLine' => $attributes];
        }
        $this->updateMessage();
    }

    public function getStartLine() : int {
        return $this->attributes['startLine'] ?? -1;
    }

    public function setStartLine(int $line): void {
        $this->attributes['startLine'] = $line;
        $this->updateMessage();
    }

    protected function updateMessage() {
        $this->message = $this->rawMessage;
        if (-1 === $this->getStartLine()) {
            $this->message .= ' on unknown line';
        } else {
            $this->message .= ' on line ' . $this->getStartLine();
        }
        if (isset($this->attributes['dump'])) {
            $this->message .= " with dumped data: ";
            foreach ($this->attributes['dump'] as $dump) {
                ob_start();
                var_dump($dump);
                $this->message .= "\n" . ob_get_clean();
            }
        }
    }
}