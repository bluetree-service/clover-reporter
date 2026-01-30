<?php

namespace CloverReporter\Render;

use CloverReporter\Console\Style;

interface RenderInterface
{
    public function __construct(array $options, array $infoList, Style $style);
    
    public function displayCoverage(): void;
    
    public function shortReport(): self;

    public function fullReport(): self;

    public function summary(float $startTime): self;
}
