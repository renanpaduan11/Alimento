<?php
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: conexao.php (Conexão TiDB Cloud / Vercel, 6 Tabelas e Auto-Sync Inteligente com dados.php)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/dados.php';

// ===================================================================
// CONFIGURAÇÕES DO TIDB CLOUD (PREENCHIDAS COM SEUS DADOS)
// ===================================================================
$host   = '://tidbcloud.com';
$port   = '4000';
$user   = '3obu4Yuw3KXeH6t.root';
$pass   = '0UvCcg70xMv0ypSk'; // <-- Clique em "Reset Password" no TiDB e cole a senha aqui
$dbname = 'db_sustentabilidade';

try {
      // 1. Conexão inicial ao servidor TiDB Cloud (forçando SSL/TLS obrigatório)
    $options = [
        PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND       => "SET NAMES utf8mb4",
        PDO::MYSQL_ATTR_SSL_CA             => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ];

    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, $options);


    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, $options);

    // 2. Autocriação do Banco de Dados
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // 3. Tabela 1: usuarios_adm
    $pdo->exec("CREATE TABLE IF NOT EXISTS `usuarios_adm` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario` VARCHAR(50) NOT NULL UNIQUE,
        `senha_hash` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 4. Tabela 2: receitas
    $pdo->exec("CREATE TABLE IF NOT EXISTS `receitas` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `titulo` VARCHAR(150) NOT NULL,
        `categoria` ENUM('Aproveitamento Integral', 'Vegana/Vegetariana', 'Rápida & Econômica', 'Tradicional Regional') NOT NULL,
        `ingredientes` TEXT NOT NULL,
        `modo_preparo` TEXT NOT NULL,
        `dica_sustentavel` TEXT NOT NULL,
        `autor` VARCHAR(100) NOT NULL,
        `imagem_url` TEXT NOT NULL,
        `status` ENUM('aprovado', 'pendente') DEFAULT 'aprovado',
        `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 5. Tabela 3: locais_sustentaveis
    $pdo->exec("CREATE TABLE IF NOT EXISTS `locais_sustentaveis` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(150) NOT NULL,
        `tipo` VARCHAR(80) NOT NULL,
        `cidade` VARCHAR(80) NOT NULL,
        `descricao` TEXT NOT NULL,
        `endereco` VARCHAR(255) NOT NULL,
        `link_maps` TEXT NOT NULL,
        `imagem_url` TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 6. Tabela 4: comentarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS `comentarios` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `tipo_origem` ENUM('receita', 'local') NOT NULL,
        `item_id` INT NOT NULL,
        `nome_usuario` VARCHAR(100) NOT NULL,
        `nota` INT NOT NULL CHECK (`nota` BETWEEN 1 AND 5),
        `texto` TEXT NOT NULL,
        `status` ENUM('pendente', 'aprovado') DEFAULT 'pendente',
        `data_comentario` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 7. Tabela 5: guia_plantio
    $pdo->exec("CREATE TABLE IF NOT EXISTS `guia_plantio` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `cultura` VARCHAR(120) NOT NULL,
        `estacao` VARCHAR(60) NOT NULL,
        `espaco` VARCHAR(80) NOT NULL,
        `tempo_colheita` VARCHAR(80) NOT NULL,
        `necessidade_sol` VARCHAR(100) NOT NULL,
        `frequencia_rega` VARCHAR(120) NOT NULL,
        `dica_regional` TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 8. Tabela 6: mitos_verdades (30 Questões Cientificamente Verificadas no Banco de Dados)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `mitos_verdades` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `pergunta` TEXT NOT NULL,
        `resposta_correta` ENUM('Mito', 'Verdade') NOT NULL,
        `imagem` TEXT NOT NULL,
        `explicacao` TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // ===================================================================
    // AUTO-SYNC INTELIGENTE COM O ARQUIVO dados.php (INSERE E ATUALIZA)
    // ===================================================================

    // Seed 1: Usuário Admin Padrão (admin / admin123)
    $stmtCheckAdmin = $pdo->query("SELECT COUNT(*) FROM `usuarios_adm`");
    if ((int)$stmtCheckAdmin->fetchColumn() === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtInsAdmin = $pdo->prepare("INSERT INTO `usuarios_adm` (`usuario`, `senha_hash`) VALUES (:u, :s)");
        $stmtInsAdmin->execute([':u' => 'admin', ':s' => $hash]);
    }

    // Seed 2: Receitas (Insere novas e atualiza fotos/ingredientes das existentes sem apagar comentários)
    if (!empty($DADOS_RECEITAS)) {
        $titulosNoBanco = $pdo->query("SELECT `titulo` FROM `receitas`")->fetchAll(PDO::FETCH_COLUMN);

        $stmtInsRec = $pdo->prepare("INSERT INTO `receitas` 
            (`titulo`, `categoria`, `ingredientes`, `modo_preparo`, `dica_sustentavel`, `autor`, `imagem_url`, `status`) 
            VALUES (:titulo, :categoria, :ingredientes, :modo_preparo, :dica_sustentavel, :autor, :imagem_url, :status)");

        $stmtUpdRec = $pdo->prepare("UPDATE `receitas` SET 
            `categoria` = :categoria,
            `ingredientes` = :ingredientes,
            `modo_preparo` = :modo_preparo,
            `dica_sustentavel` = :dica_sustentavel,
            `autor` = :autor,
            `imagem_url` = :imagem_url
            WHERE `titulo` = :titulo");

        foreach ($DADOS_RECEITAS as $r) {
            if (!in_array($r['titulo'], $titulosNoBanco, true)) {
                $stmtInsRec->execute([
                    ':titulo'           => $r['titulo'],
                    ':categoria'        => $r['categoria'],
                    ':ingredientes'     => $r['ingredientes'],
                    ':modo_preparo'     => $r['modo_preparo'],
                    ':dica_sustentavel' => $r['dica_sustentavel'],
                    ':autor'            => $r['autor'],
                    ':imagem_url'       => $r['imagem_url'],
                    ':status'           => $r['status']
                ]);
            } else {
                $stmtUpdRec->execute([
                    ':titulo'           => $r['titulo'],
                    ':categoria'        => $r['categoria'],
                    ':ingredientes'     => $r['ingredientes'],
                    ':modo_preparo'     => $r['modo_preparo'],
                    ':dica_sustentavel' => $r['dica_sustentavel'],
                    ':autor'            => $r['autor'],
                    ':imagem_url'       => $r['imagem_url']
                ]);
            }
        }
    }

    // Seed 3: Locais Sustentáveis (Insere novos e garante endereços/fotos atualizados)
    if (!empty($DADOS_LOCAIS)) {
        $locaisNoBanco = $pdo->query("SELECT `nome` FROM `locais_sustentaveis`")->fetchAll(PDO::FETCH_COLUMN);

        $stmtInsLoc = $pdo->prepare("INSERT INTO `locais_sustentaveis` 
            (`nome`, `tipo`, `cidade`, `descricao`, `endereco`, `link_maps`, `imagem_url`) 
            VALUES (:nome, :tipo, :cidade, :descricao, :endereco, :link_maps, :imagem_url)");

        $stmtUpdLoc = $pdo->prepare("UPDATE `locais_sustentaveis` SET 
            `tipo` = :tipo,
            `cidade` = :cidade,
            `descricao` = :descricao,
            `endereco` = :endereco,
            `link_maps` = :link_maps,
            `imagem_url` = :imagem_url
            WHERE `nome` = :nome");

        foreach ($DADOS_LOCAIS as $l) {
            $paramsLoc = [
                ':nome'       => $l['nome'],
                ':tipo'       => $l['tipo'],
                ':cidade'     => $l['cidade'],
                ':descricao'  => $l['descricao'],
                ':endereco'   => $l['endereco'],
                ':link_maps'  => $l['link_maps'],
                ':imagem_url' => $l['imagem_url']
            ];
            if (!in_array($l['nome'], $locaisNoBanco, true)) {
                $stmtInsLoc->execute($paramsLoc);
            } else {
                $stmtUpdLoc->execute($paramsLoc);
            }
        }
    }

    // Seed 4: Comentários Iniciais (Insere apenas se a tabela estiver vazia)
    if (!empty($DADOS_COMENTARIOS)) {
        $stmtCheckCom = $pdo->query("SELECT COUNT(*) FROM `comentarios`");
        if ((int)$stmtCheckCom->fetchColumn() === 0) {
            $stmtInsCom = $pdo->prepare("INSERT INTO `comentarios` 
                (`tipo_origem`, `item_id`, `nome_usuario`, `nota`, `texto`, `status`) 
                VALUES (:tipo_origem, :item_id, :nome_usuario, :nota, :texto, :status)");

            foreach ($DADOS_COMENTARIOS as $c) {
                $stmtInsCom->execute([
                    ':tipo_origem'  => $c['tipo_origem'],
                    ':item_id'      => $c['item_id'],
                    ':nome_usuario' => $c['nome_usuario'],
                    ':nota'         => $c['nota'],
                    ':texto'        => $c['texto'],
                    ':status'       => $c['status'] ?? 'aprovado'
                ]);
            }
        }
    }
    // Seed 5: Guia de Plantio (Insere novas culturas e atualiza dicas existentes)
    if (!empty($DADOS_PLANTIO)) {
        $culturasNoBanco = $pdo->query("SELECT `cultura` FROM `guia_plantio`")->fetchAll(PDO::FETCH_COLUMN);

        $stmtInsPlantio = $pdo->prepare("INSERT INTO `guia_plantio` 
            (`cultura`, `estacao`, `espaco`, `tempo_colheita`, `necessidade_sol`, `frequencia_rega`, `dica_regional`) 
            VALUES (:cultura, :estacao, :espaco, :tempo_colheita, :necessidade_sol, :frequencia_rega, :dica_regional)");

        $stmtUpdPlantio = $pdo->prepare("UPDATE `guia_plantio` SET 
            `estacao` = :estacao,
            `espaco` = :espaco,
            `tempo_colheita` = :tempo_colheita,
            `necessidade_sol` = :necessidade_sol,
            `frequencia_rega` = :frequencia_rega,
            `dica_regional` = :dica_regional
            WHERE `cultura` = :cultura");

        foreach ($DADOS_PLANTIO as $p) {
            $paramsPlantio = [
                ':cultura'         => $p['cultura'],
                ':estacao'         => $p['estacao'],
                ':espaco'          => $p['espaco'],
                ':tempo_colheita'  => $p['tempo_colheita'],
                ':necessidade_sol' => $p['necessidade_sol'],
                ':frequencia_rega' => $p['frequencia_rega'],
                ':dica_regional'   => $p['dica_regional']
            ];
            if (!in_array($p['cultura'], $culturasNoBanco, true)) {
                $stmtInsPlantio->execute($paramsPlantio);
            } else {
                $stmtUpdPlantio->execute($paramsPlantio);
            }
        }
    }

    // Seed 6: Mitos e Verdades (Insere novas perguntas e atualiza explicações/imagens)
    if (!empty($DADOS_MITOS)) {
        $perguntasNoBanco = $pdo->query("SELECT `pergunta` FROM `mitos_verdades`")->fetchAll(PDO::FETCH_COLUMN);

        $stmtInsMito = $pdo->prepare("INSERT INTO `mitos_verdades` 
            (`pergunta`, `resposta_correta`, `imagem`, `explicacao`) 
            VALUES (:pergunta, :resposta_correta, :imagem, :explicacao)");

        $stmtUpdMito = $pdo->prepare("UPDATE `mitos_verdades` SET 
            `resposta_correta` = :resposta_correta,
            `imagem` = :imagem,
            `explicacao` = :explicacao
            WHERE `pergunta` = :pergunta");

        foreach ($DADOS_MITOS as $m) {
            $paramsMito = [
                ':pergunta'         => $m['pergunta'],
                ':resposta_correta' => $m['resposta_correta'],
                ':imagem'           => $m['imagem'],
                ':explicacao'       => $m['explicacao']
            ];
            if (!in_array($m['pergunta'], $perguntasNoBanco, true)) {
                $stmtInsMito->execute($paramsMito);
            } else {
                $stmtUpdMito->execute($paramsMito);
            }
        }
    }

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
