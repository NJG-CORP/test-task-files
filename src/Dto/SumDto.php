<?php


namespace App\Dto;


use Brick\Math\BigDecimal;

class SumDto
{
    public string $path;
    public BigDecimal $sum;

    public function __construct(string $path, BigDecimal $sum)
    {
        $this->path = $path;
        $this->sum  = $sum;
    }
}