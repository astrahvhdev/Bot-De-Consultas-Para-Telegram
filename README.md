Descrição

O Bot de Consultas do Telegram é uma ferramenta automatizada desenvolvida em PHP para realizar consultas rápidas e eficientes a partir de bases de dados ou APIs externas. Ele permite que usuários obtenham informações valiosas diretamente no Telegram, tornando o processo de busca mais acessível e conveniente.

Recursos Principais:

Consulta de dados em tempo real a partir de APIs ou banco de dados.

Interface intuitiva com comandos simplificados.

Respostas formatadas para melhor compreensão.

Suporte para diversas categorias de consulta.

Controle de acesso com autenticação opcional para administradores.

Logs de consultas para monitoramento e análise.

Requisitos

Antes de iniciar a instalação, verifique se você possui os seguintes requisitos:

Servidor com suporte a PHP 7.x ou superior.

Biblioteca cURL ativada.

Acesso à API ou banco de dados relevante.

Token de bot do Telegram (fornecido pelo @BotFather).

Instalação

Siga os passos abaixo para configurar o bot:

Clone o repositório:

git clone https://github.com/seuusuario/bot-consultas-telegram.git
cd bot-consultas-telegram

Configure o arquivo config.php com suas credenciais:

<?php
$token = "SEU_TOKEN_AQUI";
$api_url = "https://api.telegram.org/bot$token/";
$api_key = "SUA_CHAVE_DE_API";
?>

Configure um webhook para receber mensagens automaticamente (opcional):

curl -X POST "https://api.telegram.org/botSEU_TOKEN_AQUI/setWebhook?url=SEU_URL/webhook.php"

Hospede o bot em um servidor PHP ou utilize um serviço como Heroku, VPS ou hospedagem compartilhada com suporte a cron jobs.

Uso

No Telegram, envie um dos comandos abaixo para interagir com o bot:

/start - Inicia o bot e exibe informações sobre seu funcionamento.

/consulta_cpf <número> - Realiza uma consulta de CPF.

/consulta_cns <número> - Realiza uma consulta de CNS.

/consulta_falecimento <data> - Verifica informações sobre falecimentos.

/consulta_nascimento <data> - Consulta registros de nascimento.

/consulta_nome <nome> - Consulta registros por nome.

/adicionar_admin <username> - Adiciona um novo administrador (somente admins autorizados).

Configuração Avançada

Se desejar integrar o bot com um banco de dados MySQL ou outra API, edite o arquivo config.php e ajuste as configurações necessárias. Certifique-se de que as permissões de acesso estejam corretas.

Contribuição

Se desejar contribuir para o desenvolvimento do bot, siga estas etapas:

Faça um fork do repositório.

Crie uma nova branch: git checkout -b minha-feature

Faça as alterações e commit: git commit -m "Minha melhoria"

Envie para o repositório: git push origin minha-feature

Abra um Pull Request.

Licença

Este projeto é distribuído sob a licença MIT. Para mais detalhes, consulte o arquivo LICENSE.

Contato

Caso tenha dúvidas ou precise de suporte, entre em contato pelo Telegram: @SeuUsuario.
