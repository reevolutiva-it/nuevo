<?php

// Path: wp-content/plugins/coursefac-integration/inc/api-key-mannager.php

/**
 * Esta función se encarga de gestionar la API KEY de Coursefactory.
 * Si el parámetro $action es "add" o "update" se añade o actualiza el valor del meta_key cfact_ld_api_key.	
 * Si el parámetro $action es "delete" se elimina el valor del meta_key cfact_ld_api_key.
 *
 * @param string $action
 * @param mixed $data
 * @return mixed
 */
function cfact_ld_api_key_mannger($action, $data = null ) {
    // Obtener el valor actual del meta_key cfact_ld_api_key
    $current_value = get_option('cfact_ld_api_key');

    if($action == "add" || $action == "update"){

           // Comprobar si el valor existe
            if ($current_value) {
                // Actualizar el valor existente
                update_option('cfact_ld_api_key', $data);
            } else {
                // Crear un nuevo valor
                add_option('cfact_ld_api_key', $data);
            }

            // Obtener el valor actualizado
            $updated_value = get_option('cfact_ld_api_key');

            return $updated_value;

    }

    if($action == "delete"){

        // Eliminar el valor
        $deleted = delete_option('cfact_ld_api_key');

        // Devolver el valor actualizado
        return $deleted;

    }

    if($action == "get"){

        // Devolver el valor actualizado
        return $current_value;

    }
}
