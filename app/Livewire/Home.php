<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public $textoPlano = '';
    public $code = '';
    public $matchesd;
    public $quotes;
    public $lenaQuotes;
    public $aQuotes = [];
    public $grupo1;
    public $grupo2;
    public $grupo3;
    public $grupo4;
    public $grupo5;
    public $grupo6;
    public $grupo7;
    public $grupo8;
    public $errors = [];

    public function compile()
    {
        $source = $this->textoPlano;
        $this->errors = [];

        $pattern = <<<PATTERN
        ~
            (?<comentarios>//.*?$) |
            (?<comentarios_doble>/\*.*?\*/) |
            (?<comillas_dobles>"(?:\\.|[^\"])*") |
            (?<comillas_simples>'(?:\\.|[^'])*') |
            (?<operadores>>=|<<=|>=|<=|==|!=|\+\+|--|\|\||&&|<<|>>|[-+*/%=<>&|;]) |
            (?<reservadas>\b(printf|int|float|double|char|bool|if|else|for|while|do|return|void|break|continue|switch|case|default|const|class|public|using|namespace)\b) |
            (?<identificadores>\b[a-zA-Z_][a-zA-Z0-9_]*\b) |
            (?<numeros>\d+(?:\.\d+)?) |            
        ~xms
        PATTERN;

        $matches = [];
        $this->matchesd = $matches;
        preg_match_all($pattern, $source, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $grupos = array_fill(1, 8, 0);
        foreach ($matches as $match) {
            for ($i = 1; $i <= 8; $i++) {
                if (!empty($match[$i]) && $match[$i][1] >= 0) {
                    $grupos[$i]++;
                }
            }
        }

        $this->grupo1 = $grupos[1];
        $this->grupo2 = $grupos[2];
        $this->grupo3 = $grupos[3];
        $this->grupo4 = $grupos[4];
        $this->grupo5 = $grupos[5];
        $this->grupo6 = $grupos[6];
        $this->grupo7 = $grupos[7];
        $this->grupo8 = $grupos[8];

        $sourceWithoutComments = preg_replace('~//.*?$|/\*.*?\*/~ms', '', $source);
        if (preg_match_all('/[^\s{};][\r\n]+/', $sourceWithoutComments, $missingSemicolons, PREG_OFFSET_CAPTURE)) {
            foreach ($missingSemicolons[0] as $missing) {
                $this->errors[] = $this->getErrorPosition($source, $missing[1], 'Falta punto y coma (;)');
            }
        }

        if (preg_match_all('/;;+/', $source, $extraSemicolons, PREG_OFFSET_CAPTURE)) {
            foreach ($extraSemicolons[0] as $extra) {
                $this->errors[] = $this->getErrorPosition($source, $extra[1], 'Punto y coma de más (;;)');
            }
        }

        if (!preg_match('/\bmain\s*\([^)]*\)\s*\{/', $source)) {
            $this->errors[] = "Error léxico: undefined reference to `main'";
        }

        //dd($invalidTokens);
        $openBraces = substr_count($source, '{');
        $closeBraces = substr_count($source, '}');
        $openPar = substr_count($source, '(');
        $closePar = substr_count($source, ')');

        $this->quotes = substr_count($source, '"');
        $this->aQuotes[] = $this->quotes;
        foreach ($this->aQuotes as $key => $item) {
            if ($item == 0) {
                unset($this->aQuotes[$key]);
            }
            if (count($this->aQuotes) > 1) {
                if ($item == $this->aQuotes[0]) {
                    if ($key != 0) {
                        unset($this->aQuotes[$key]);
                    }
                }
            }
        }

        if (count($this->aQuotes) > 2) {
            unset($this->aQuotes[2]);
        }
        if ($this->quotes == 0) {
            $this->aQuotes = [];
        }
        if (count($this->aQuotes) == 2) {
            //dd($this->aQuotes[0] > $this->quotes);
            if ($this->aQuotes[1] > $this->quotes) {
                $this->errors[] = "Error léxico: Faltan camillas";
            } elseif ($this->aQuotes[0] > $this->quotes) {
                $this->errors[] = "Error léxico: Falta una camilla";
            }
        }
        $this->lenaQuotes = count($this->aQuotes);


        if ($openPar > $closePar) {
            $this->errors[] = "Error léxico: Falta " . ($openPar - $closePar) . " Paréntesis de cierre `)`.";
        } elseif ($closePar > $openPar) {
            $this->errors[] = "Error léxico: Hay " . ($closePar - $openPar) . " Paréntesis de más `)`.";
        }

        if ($openBraces > $closeBraces) {
            $this->errors[] = "Error léxico: Falta " . ($openBraces - $closeBraces) . " llave(s) de cierre `}`.";
        } elseif ($closeBraces > $openBraces) {
            $this->errors[] = "Error léxico: Hay " . ($closeBraces - $openBraces) . " llave(s) de más `}`.";
        }

        // Resaltado de sintaxis
        $callback = function ($matches) {
            if (!empty($matches['comentarios'])) {
                return '<span class="text-gray-500 italic">' . htmlspecialchars($matches['comentarios']) . '</span>';                            
            } elseif (!empty($matches['comentarios_doble'])) {
                return '<span class="text-gray-500 italic">' . htmlspecialchars($matches['comentarios_doble']) . '</span>';
            } elseif (!empty($matches['comillas_dobles'])) {
                return '<span class="text-green-400">' . htmlspecialchars($matches['comillas_dobles']) . '</span>';
            } elseif (!empty($matches['comillas_simples'])) {
                return '<span class="text-green-400">' . htmlspecialchars($matches['comillas_simples']) . '</span>';
            } elseif (!empty($matches['operadores'])) {
                return '<span class="text-purple-400">' . htmlspecialchars($matches['operadores']) . '</span>';
            } elseif (!empty($matches['reservadas'])) {
                return '<span class="text-blue-400 font-bold">' . htmlspecialchars($matches['reservadas']) . '</span>';
            } elseif (!empty($matches['identificadores'])) {
                return '<span class="text-red-400">' . htmlspecialchars($matches['identificadores']) . '</span>';
            } elseif (!empty($matches['numeros'])) {
                return '<span class="text-yellow-300">' . htmlspecialchars($matches['numeros']) . '</span>';
            } else {
                return '<span class="text-white">' . htmlspecialchars($matches[0]) . '</span>';
            }
        };

        $highlighted = preg_replace_callback($pattern, $callback, $source);
        $this->code = $highlighted;
        $sourced = preg_replace('/\s+/', ' ', $source);
        $doblec = explode(' ', $sourced);
        $resDoblec = in_array('<iostream>;', $doblec);
        $resDoblecd = in_array('<iostream>;;', $doblec);
        if ($resDoblec) {
            $lengthRes = strlen('#include <iostream>;') - 1;
            $this->errors[] = $this->getErrorPosition($source, $lengthRes, 'Punto y coma de más');
        }
        //dd($this->errors);
        if ($this->errors) {
            $arrayError = explode(' ', $this->errors[0]);
            $res = in_array('línea1,', $arrayError);

            if ($res && $resDoblec == false && $resDoblecd == false) {
                unset($this->errors[0]);
            }
        }
    }

    private function getErrorPosition($source, $position, $text)
    {
        $lines = explode("\n", substr($source, 0, $position));
        $lineNumber = count($lines);
        $column = strlen(end($lines)) + 1;

        return "Error léxico en línea$lineNumber, columna $column: '"
            . htmlspecialchars(substr($text, 0, 30)) . "'";
    }

    public function render()
    {
        return view('livewire.home');
    }
}
