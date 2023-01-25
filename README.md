# pix-whmcs
Pagamento via Pix para WHMCS

#Instruções
Faça download e descompacte dentro da pasta /modules/gateways/ de seu Whmcs.
Entre em: Portais de Pagamento e ative o modulo Pix (Oi Rodrigo).
Entre com sua Chave Pix, Beneficiário, Instituição e Cidade.
Configure um código de prefixo para diferenciar as transações recebidas.

Para enviar Código PIX por email basta colar o código abaixo no modelo de email

{if $invoice_payment_method=="Pix (Oi Rodrigo)"}{$invoice_payment_link}{/if}
