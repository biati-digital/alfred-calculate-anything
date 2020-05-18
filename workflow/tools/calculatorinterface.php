<?php

namespace Workflow\Tools;

interface CalculatorInterface
{
    /**
     * Should process
     * make sure the query must be processed
     * otherwise stop execution
     *
     * @return boolean
     */
    public function shouldProcess();

    /**
     * Process query
     * this method should contain the logic
     * to process the provided query
     *
     * @return string|array
     */
    public function processQuery();

    /**
     * Output
     * every tool should return an output
     * that is formatted to be displayed by Alfred
     * it accepts a variable that contains
     * the information processed by the converter
     *
     * @param string|array
     * @return array
     */
    public function output($processed);
}
