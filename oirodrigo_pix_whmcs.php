<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

include("oirodrigo_pix_whmcs/vendor/autoload.php");
include("oirodrigo_pix_whmcs/pixWHMCS.php");

function oirodrigo_pix_whmcs_MetaData() {
    return array(
        'DisplayName' => 'Pix (Oi Rodrigo)',
        'APIVersion' => '1.0',
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    );
}


function oirodrigo_pix_whmcs_config() {
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Pix (Oi Rodrigo)',
        ),
        // a text field type allows for single line text input
        'chavePix' => array(
            'FriendlyName' => 'Chave Pix',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'CPF/CNPJ | Telefone | Email | Aleátoria',
        ),
        // a text field type allows for single line text input
        'beneficiario' => array(
            'FriendlyName' => 'Beneficiário',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Digite seu nome até 25 caracteres',
        ),
        'instituicao' => array(
            'FriendlyName' => 'Instituição',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Digite o nome do banco até 25 caracteres',
        ),
        // a text field type allows for single line text input
        'cidade' => array(
            'FriendlyName' => 'Cidade',
            'Type' => 'text',
            'Size' => '15',
            'Default' => '',
            'Description' => 'Digite sua Cidade até 15 caracteres',
        ),
        // a text field type allows for single line text input
        'prefixo' => array(
            'FriendlyName' => 'Prefixo',
            'Type' => 'text',
            'Size' => '8',
            'Default' => 'Fatura #',
            'Description' => 'Digite o Prefixo de seu identificador. Tamanho Máximo: 8',
        ),
        'aviso' => array(
            'FriendlyName' => 'Autor',
            'Type' => 'label',
            'Size' => '15',
            'Description' => '<h3><b>Oi Rodrigo</b></h3><p><b>Especialista em Desenvolvimento e Hospedagem Web</b></p><h3>Sugestões, Dúvidas?</h3><p>Entrem em contato em: <a href="https://oirodrigo.com.br">https://oirodrigo.com.br</a></p>',
        ),
    );
}

function oirodrigo_pix_whmcs_link($params) {

    // Gateway Configuration Parameters

    $chavePix = $params['chavePix'];
    $beneficiario = $params['beneficiario'];
    $instituicao = $params['instituicao'];
    $cidade = $params['cidade'];
    $prefixo = $params['prefixo'];

    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    //gera o qrcode do PIX

    define("pix_chave", $chavePix);
    define("pix_nome", $beneficiario);
    define("pix_cidade", $cidade);
    define("tamanho_qrCode", 200);

    $identificador = str_pad( substr( ( $prefixo . $invoiceId ) , 0, 8 ) , 8 , '*' , STR_PAD_LEFT);

    $pix       = \PhpPix\Pix::generateCode( $chavePix, $beneficiario, $cidade, $identificador , $amount );
    $Img       = \PhpPix\Pix::generateQrCodePIX( $pix );

    $urlQrCodePix = "data:image/png;base64,". base64_encode( $Img );

    $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);


    $htmlOutput = '<script type="text/javascript">
        function copiarPix() {

            try {
                var aux = document.createElement("input");

                link = "' . $pix . '";

                // Get the text from the element passed into the input
                aux.setAttribute("value", link);

                // Append the aux input to the body
                document.body.appendChild(aux);

                // Highlight the content
                aux.select();

                // Execute the copy command
                document.execCommand("copy");

                // Remove the input from the body
                document.body.removeChild(aux);

                document.getElementById("pix_copiado").innerHTML = \'<span style="color:green;">Copiado!</span>\';
                // alert("Código Pix Copiado!");

            } catch (e) {
                //alert("Erro");
            }

        }
    </script>';

    $htmlOutput .= '<p>' . '<img src="'.$urlQrCodePix.'">' . '</p>'
            . '<p><strong>Pix Copia e Cola:</strong><br><i id="pix_copiado" onclick="javascript:copiarPix();">(Clique para copiar o código)</i></p>'
            . '<textarea readonly name="textarea" style="cursor:pointer;border:solid 2px #ccc;border-radius:6px;padding:6px;background:#fff;font-family:sans-serif;font-size:9px;overflow:hidden;word-break:break-all;" onclick="javascript:copiarPix();"
   rows="5" cols="30"
   minlength="10" maxlength="20">' . $pix . '</textarea>'
            . '</p><p/><hr /><h3><b>Valor: ' . $formatter->formatCurrency($amount, 'BRL') . '</b></h3>'
            . '<p style="font-size:12px; margin-bottom: 5px;"><i>Chave Pix:</i> <b>'.$chavePix.'</b><br>'
            . '<p style="font-size:12px; margin-bottom: 5px;"><i>Beneficiário:</i> <b>'.$beneficiario.'</b><br>'
            . '<p style="font-size:12px; margin-bottom: 5px;"><i>Instituição:</i> <b>'.$instituicao.'</b><br>'
            . '<p><b></b></p><style>.payment-btn-container{background: #f1f1f1;padding: 30px 15px;border: solid 3px #ccc;border-radius:6px;}</style>';

    return $htmlOutput;
}