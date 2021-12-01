<?php

function fireplace_input_number_year($label, $name, $isRequired = true)
{
    fireplace_input_number($label, $name, '1900', '2100', '1', $isRequired);
}