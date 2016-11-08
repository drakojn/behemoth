<?php
declare(strict_types = 1);
namespace Drakojn\Behemoth\Helper\Environment;

use Cekurte\Environment\Contract\FilterInterface;

class Filter implements FilterInterface
{
    protected $preffix;

    public function __construct(string $preffix)
    {
        $this->preffix = $preffix;
    }

    public function filter($data):array
    {
        return array_filter($data, [$this, 'callback']);
    }

    public function callback($item):bool
    {
        return strpos($item, $this->preffix) === 0;
    }

    public function normalizeName(string $name):string
    {
        return str_replace('_', '.', strtolower($name));
    }

    public function transformFromJson(string $value)
    {
        if (strlen($value) === strlen(trim($value, '[{}]'))) {
            return $value;
        }
        return json_decode($value);
    }
}
