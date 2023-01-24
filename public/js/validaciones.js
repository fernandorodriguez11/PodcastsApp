$(function(){
    
    $("#registro_form_password").on('change', function () {
            
            $VAL = $("#registro_form_password").val();
            $mensaje = "";

            $pattern = /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/;

            if ($VAL.match($pattern)) {
                $mensaje = "";
            }else{
                $mensaje = 'La contraseña debe de tener al menos 1 mayuscula minuscula y numero';

                if($VAL == ""){
                    $mensaje = "";
                }
            }

            $("#textoValidacion").text($mensaje);
     });

     $("#registro_form_email").on('change', function () {
            
        $VAL = $("#registro_form_email").val();
        $mensaje = "";

        $pattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

        if ($VAL.match($pattern)) {
            $mensaje = "";
        }else{
            $mensaje = 'Email incorrecto';
        }

        $("#emailError").text($mensaje);
 });

})