<?php

    foreach($alertas as $key => $alerta): // recorre el arreglo
        foreach($alerta as $mensaje):     // recorre loa mensajes de error
?>

    <div class="alerta <?php echo $key; ?>"><?php echo $mensaje; ?></div>

<?php
         endforeach;
    endforeach;
?>