<?php

/**
 * Copyright (C) 2017 Bynq.io B.V.
 * All Rights Reserved
 *
 * This file is part of the BynqIO\CalculatieTool.com.
 *
 * Content can not be copied and/or distributed without the express
 * permission of the author.
 *
 * @package  CalculatieTool
 * @author   Yorick de Wid <y.dewid@calculatietool.com>
 */

namespace BynqIO\CalculatieTool\Core\Flow;

class CalculationFlow extends BaseFlow
{
    protected $steps = [
        'details',
        'calculation',
        'quotations',
        'estimate',
        'less',
        'more',
        'invoice',
        'result',
    ];

}
