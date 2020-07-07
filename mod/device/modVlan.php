<pre>
<?php

  function keyscut($arr)
    {
    $new_arr = array();
    reset($arr);
    for ($i = 0; $i < count($arr); $i++)
      {
      $val = $arr[key($arr)];
      $key = key($arr);
      $key = substr(strrchr($key, '.'), 1);
      $new_arr[$key] = $val;
      next($arr);
      }
    return $new_arr;
    }


$ip = '10.11.21.12';
$rcomm = 'public';
$dot1qVlanStaticUntaggedPorts = @snmpwalkoid($ip, $rcomm, ".1.3.6.1.2.1.17.7.1.4.3.1.4");


print_r($dot1qVlanStaticUntaggedPorts);


$dot1qVlanStaticUntaggedPorts = keyscut($dot1qVlanStaticUntaggedPorts);


print_r($dot1qVlanStaticUntaggedPorts);
?>