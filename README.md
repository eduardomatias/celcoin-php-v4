# Celcoin API-PHP
API Php para recargas, pagamento de contas e muito mais!

## Instalação
### Composer
Se você já conhece o **Composer** (o que é extremamente recomendado), simplesmente adicione a dependência ao seu projeto.

```
composer require toolstore/celcoin @dev
```

## Obtendo as Operadoras

```
use Celcoin\CelcoinApi;

$celcoin = new CelcoinApi(YOUR CLIENT_ID, YOUR SECRET_KEY);
print_r($celcoin->getProviders());

```

## Obtendo os valores das operadoras

```
use Celcoin\CelcoinApi;

$celcoin = new CelcoinApi();
$operadoras = $celcoin->getProviders();

if(is_array($operadoras)){
    foreach ($operadoras as $operadora){
       print_r($celcoin->getProvidersValues('61', $operadora['providerId'])); //Código Regional de Brasília
    }
}

```

## Depuração

Todas as tentativas de transação com o WebService e seus conteúdos podem
ser verificados ao setar a variável `debug` para `true`.

obs: o ambinte de homolgação retorna dados estáticos
```
use Celcoin\CelcoinApi;

$celcoin = new CelcoinApi();
$celcoin->setDebug(true);

print_r($celcoin->getProviders());

```

[Documentation](https://apihmlg.celcoin.com.br/swagger/ui/index)
