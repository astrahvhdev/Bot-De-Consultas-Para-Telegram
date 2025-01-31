<?php

// Token do bot
$token = "7703358543:AAGet9p5NFlCd20dsecH4HbiMhsoZ1oYvRs";

// URL base da API do Telegram
$api_url = "https://api.telegram.org/bot$token/";

// Lista de administradores do bot (baseado em usernames do Telegram)
$administradores = ["Sethking7", "astrahvhdev", "metodos05km", "ZeroXusee1995"];  // Adicione os usernames dos administradores aqui

// Função para enviar requisições ao Telegram
function bot($method, $parameters = []) {
    global $api_url;
    $url = $api_url . $method;

    $options = [
        'http' => [
            'method'  => 'POST',
            'content' => json_encode($parameters),
            'header'  => "Content-Type: application/json\r\n" . 
                         "Accept: application/json\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return json_decode($result, true);
}

// Função para verificar se o usuário é administrador
function isAdministrador($username) {
    global $administradores;
    return in_array($username, $administradores);
}

// Função para adicionar administrador
function adicionarAdministrador($username) {
    global $administradores;
    if (!in_array($username, $administradores)) {
        $administradores[] = $username;
        return "Administrador {$username} adicionado com sucesso.";
    }
    return "{$username} já é um administrador.";
}

// Função para processar as consultas
function processarConsulta($chat_id, $comando, $parametro, $username) {
    // Verificar se o comando foi enviado por um administrador
    if ($comando == "/adicionar_admin") {
        if (isAdministrador($username)) {
            // Adicionar novo administrador
            $mensagem = adicionarAdministrador($parametro);
            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => $mensagem,
                "parse_mode" => 'Markdown'
            ]);
            return;
        } else {
            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => "❌ Você não tem permissão para adicionar administradores.",
                "parse_mode" => 'Markdown'
            ]);
            return;
        }
    }

    $base_url = "https://centralbrasil.shop/apis/";
    $key = "123"; // Sua chave de API
    $resultado = "";

    // Identificar qual consulta será realizada
    switch ($comando) {
        case "/consulta_cpf":
            $url = $base_url . "cpf.php";
            $parametros = ["key" => $key, "cpf" => $parametro];
            $resultado = realizarConsulta($url, $parametros);
            break;

        case "/consulta_cns":
            $url = $base_url . "cns.php";
            $parametros = ["key" => $key, "cns" => $parametro];
            $resultado = realizarConsulta($url, $parametros);
            break;

        case "/consulta_falecimento":
            $url = $base_url . "data_falecimento.php";
            $parametros = ["key" => $key, "data_falecimento" => $parametro];
            $resultado = realizarConsulta($url, $parametros);
            break;

        case "/consulta_nascimento":
            $url = $base_url . "nascimento.php";
            $parametros = ["key" => $key, "nascimento" => $parametro];
            $resultado = realizarConsulta($url, $parametros);
            break;

        case "/consulta_nome":
            $url = $base_url . "nome.php";
            $parametros = ["key" => $key, "nome" => $parametro];
            $resultado = realizarConsulta($url, $parametros);
            break;

        default:
            bot("sendMessage", [
                "chat_id" => $chat_id,
                "text" => "❌ Comando inválido. Tente novamente.",
                "parse_mode" => 'Markdown'
            ]);
            return;
    }

    // Formatar o resultado
    if ($resultado) {
        // Exibe a resposta bruta de forma limpa
        $mensagem = formatarRespostaBruta($resultado);
    } else {
        $mensagem = "❌ Não foi possível realizar a consulta. Tente novamente mais tarde.";
    }

    // Enviar o resultado ao usuário
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => $mensagem,
        "parse_mode" => 'Markdown'
    ]);
}

// Função para formatar a resposta da API de forma legível
function formatarRespostaBruta($dados) {
    // Remover qualquer mensagem de erro (como Warning)
    $dados = preg_replace('/Warning.*\n/', '', $dados);

    // Remover tags HTML ou PHP
    $dados = strip_tags($dados);

    // Substituir os dados de resposta pela formatação mais amigável
    // Remover as aspas duplas e substituí-las por espaços
    $dados = str_replace(['{"', '"}', '",'], ["", "", "\n"], $dados);
    $dados = str_replace(':', ' =', $dados); // Para deixar o formato amigável

    // Garantir que qualquer valor vazio seja mostrado como "Não informado"
    $dados = preg_replace('/\s*\=\s*\"\"/', ' = Não informado', $dados);
    
    // Substituir as aspas duplas restantes
    $dados = str_replace('"', '', $dados);

    // Exibir o texto de forma mais amigável
    return "*Resultado da consulta:*\n\n" . $dados;
}

// Função para realizar a consulta via cURL
function realizarConsulta($url, $parametros) {
    $full_url = $url . '?' . http_build_query($parametros);
    echo "[INFO] URL gerada para a consulta: {$full_url}\n";  // Exibe a URL no log

    // Iniciar a requisição cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $full_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos
    
    // Ignorar a verificação do certificado SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    // Verificar se houve erro na requisição cURL
    if (curl_errno($ch)) {
        echo "[ERRO] Erro cURL: " . curl_error($ch) . "\n";
    }

    // Código HTTP de resposta
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "[INFO] Código HTTP: {$http_code}\n"; // Exibe o código HTTP da resposta
    echo "[INFO] Resposta bruta da API: {$response}\n"; // Exibe a resposta bruta da API

    // Verificar se a resposta foi válida e se o código HTTP é 200 (OK)
    if ($response && $http_code === 200) {
        return $response;
    } else {
        echo "[ERRO] A API não retornou uma resposta válida ou o código HTTP foi diferente de 200.\n";
    }

    return false;
}

// Função para processar o comando /start
function start($chat_id, $nome) {
    $txt = "🔹 *Bem-vindo, {$nome}*\n\n"
         . "• [Contato do Desenvolvedor](http://t.me/astrahvhdev)\n\n"
         . "Use o menu abaixo para realizar consultas:\n\n"
         . "1️⃣ *CPF:* `/consulta_cpf CPF`\n"
         . "2️⃣ *CNS:* `/consulta_cns CNS`\n"
         . "3️⃣ *Data de Falecimento:* `/consulta_falecimento DATA`\n"
         . "4️⃣ *Data de Nascimento:* `/consulta_nascimento DATA`\n"
         . "5️⃣ *Nome:* `/consulta_nome NOME`\n\n"
         . "_Exemplo:_ `/consulta_cpf 01065963149`\n"
         . "_Obs:_ Não há limite de consultas!";

    // Mudando o texto para refletir os novos comandos disponíveis
    $button[] = ['text' => "Consultas disponíveis", 'callback_data' => "consultas"];

    $menu['inline_keyboard'] = array_chunk($button, 1); // 1 botão por linha para o menu de consultas

    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => $txt,
        "reply_markup" => $menu,
        "parse_mode" => 'Markdown'
    ]);
}

// Função de consulta disponível
function consultasDisponiveis($chat_id, $message_id) {
    $txt = "Aqui estão as consultas disponíveis para você:\n\n"
         . "1️⃣ CPF: `/consulta_cpf CPF`\n"
         . "2️⃣ CNS: `/consulta_cns CNS`\n"
         . "3️⃣ Data de Falecimento: `/consulta_falecimento DATA`\n"
         . "4️⃣ Data de Nascimento: `/consulta_nascimento DATA`\n"
         . "5️⃣ Nome: `/consulta_nome NOME`\n\n"
         . "_Exemplo:_ `/consulta_cpf 01065963149`";

    bot("editMessageText", [
        "chat_id" => $chat_id,
        "message_id" => $message_id,
        "text" => $txt,
        "parse_mode" => 'Markdown'
    ]);
}

// Loop Infinito para o bot funcionar continuamente
while (true) {
    // Obter atualizações do Telegram
    $response = file_get_contents($api_url . "getUpdates?offset=" . (isset($last_update_id) ? $last_update_id + 1 : 0));
    $updates = json_decode($response, true);

    // Verificar se há atualizações
    if (isset($updates['result']) && count($updates['result']) > 0) {
        foreach ($updates['result'] as $update) {
            $last_update_id = $update['update_id']; // Atualiza o último ID processado

            // Processar mensagens recebidas
            if (isset($update['message'])) {
                $message = $update['message'];
                $chat_id = $message['chat']['id'];
                $nome = $message['from']['first_name'] ?? "Usuário";
                $texto = $message['text'] ?? "";

                echo "[INFO] Mensagem recebida de {$nome} (ID: {$chat_id}): {$texto}\n";

                // Processar comando /start ou consultas
                if (strpos($texto, "/start") === 0) {
                    start($chat_id, $nome);
                } else {
                    $partes = explode(" ", $texto, 2);
                    $comando = $partes[0];
                    $parametro = $partes[1] ?? "";
                    processarConsulta($chat_id, $comando, $parametro, $message['from']['username']);
                }
            }

            // Processar o botão de consultas disponíveis
            if (isset($update['callback_query'])) {
                $callback_query = $update['callback_query'];
                $chat_id = $callback_query['message']['chat']['id'];
                $message_id = $callback_query['message']['message_id'];
                $data = $callback_query['data'];

                if ($data == 'consultas') {
                    consultasDisponiveis($chat_id, $message_id);
                }
            }
        }
    }

    // Pausa de 2 segundos antes de verificar novamente
    sleep(2);
}
?>
