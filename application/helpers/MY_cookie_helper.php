<?php

function set_coded_permanent_cookie($name, $value)
{
  set_cookie($name, base64_encode($value), 60 * 60 * 24 * 365 * 10);
}

function set_permanent_cookie($name, $value)
{
  set_cookie($name, $value, 60 * 60 * 24 * 365 * 10);
}

function get_coded_cookie($name)
{
  return base64_decode(get_cookie($name));
}

