$(function(){


    $("#passwordR").on('change', function(){
        $mensaje = "";

        $password = $("#registro_form_password").val();
        $password_repetida = $("#passwordR").val();

        if($password_repetida != $password){
            $mensaje = "Las contraseñas no coinciden";
            $("#passwordR").css('border', '2px solid red');
        
        }else{
            $mensaje = '';
            $("#passwordR").css('border', '2px solid #ced4da');
        }

        $("#textoError").text($mensaje);

    });

})