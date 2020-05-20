# Celcoin API-PHP v4
API Php para recargas, pagamento de contas e muito mais!

## Instalação - Composer

```
composer require eduardomatias/celcoin-php-v4 @dev
```

### Celcoin.class.php

```php
<?php

$celcoin = new Celcoin();

// retorna a operadora e os valores disponiveis para recarga
$celcoin->valoresOperadora($ddd, $numero);

// realiza recarga
$celcoin->recaregar($valor, $ddd, $numero, $cod_operadora, $assinante, $cpf_cnpj, $capturar = true);

// captura recarga
$celcoin->capturar($transaction_id);

```

[sandbox/swagger](https://sandbox-apicorp.celcoin.com.br/swagger/index.html)

[toolstore/celcoin-php v1](https://github.com/toolstore/celcoin-php)