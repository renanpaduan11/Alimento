<?php
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: conexao.php (Conexão MySQL, 6 Tabelas e Auto-Sync Inteligente com dados.php)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/dados.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'db_sustentabilidade';

try {
    // 1. Conexão inicial ao servidor MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

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

    // Seed 4: Comentários Iniciais
    $stmtCheckCom = $pdo->query("SELECT COUNT(*) FROM `comentarios`");
    if ((int)$stmtCheckCom->fetchColumn() === 0 && !empty($DADOS_COMENTARIOS_INICIAIS)) {
        $stmtInsCom = $pdo->prepare("INSERT INTO `comentarios` 
            (`tipo_origem`, `item_id`, `nome_usuario`, `nota`, `texto`, `status`) 
            VALUES (:tipo, :item_id, :nome, :nota, :texto, :status)");

        foreach ($DADOS_COMENTARIOS_INICIAIS as $c) {
            $stmtInsCom->execute([
                ':tipo'    => $c['tipo_origem'],
                ':item_id' => $c['item_id'],
                ':nome'    => $c['nome_usuario'],
                ':nota'    => $c['nota'],
                ':texto'   => $c['texto'],
                ':status'  => $c['status']
            ]);
        }
    }

    // Seed 5: Guia de Plantio (Sincroniza e remove títulos antigos com numeração fixa)
    if (!empty($DADOS_PLANTIO)) {
        $existeAntigoNumerado = (int)$pdo->query("SELECT COUNT(*) FROM `guia_plantio` WHERE `cultura` LIKE '1.%'")->fetchColumn();
        $qtdPlantioBanco      = (int)$pdo->query("SELECT COUNT(*) FROM `guia_plantio`")->fetchColumn();

        if ($existeAntigoNumerado > 0 || $qtdPlantioBanco !== count($DADOS_PLANTIO)) {
            $pdo->exec("TRUNCATE TABLE `guia_plantio`");
            $stmtInsPl = $pdo->prepare("INSERT INTO `guia_plantio` 
                (`cultura`, `estacao`, `espaco`, `tempo_colheita`, `necessidade_sol`, `frequencia_rega`, `dica_regional`) 
                VALUES (:c, :est, :esp, :t, :s, :r, :d)");
            foreach ($DADOS_PLANTIO as $p) {
                $stmtInsPl->execute([
                    ':c'   => $p['nome'],
                    ':est' => implode(', ', $p['estacoes']),
                    ':esp' => implode(', ', $p['espacos']),
                    ':t'   => $p['colheita'],
                    ':s'   => $p['sol'],
                    ':r'   => $p['rega'],
                    ':d'   => $p['dicaRegional']
                ]);
            }
        }
    }

    // Seed 6: Sincroniza as 30 Questões de Mito ou Verdade no MySQL e garante imagens atualizadas
    if (!empty($DADOS_MITOS)) {
        $qtdMitosBanco = (int)$pdo->query("SELECT COUNT(*) FROM `mitos_verdades`")->fetchColumn();
        if ($qtdMitosBanco !== count($DADOS_MITOS)) {
            $pdo->exec("TRUNCATE TABLE `mitos_verdades`");
            $stmtInsMito = $pdo->prepare("INSERT INTO `mitos_verdades` 
                (`id`, `pergunta`, `resposta_correta`, `imagem`, `explicacao`) 
                VALUES (:id, :p, :r, :img, :exp)");
            foreach ($DADOS_MITOS as $m) {
                $stmtInsMito->execute([
                    ':id'  => (int)$m['id'],
                    ':p'   => $m['pergunta'],
                    ':r'   => $m['respostaCorreta'],
                    ':img' => $m['imagem'],
                    ':exp' => $m['explicacao']
                ]);
            }
        } else {
            $stmtUpdMito = $pdo->prepare("UPDATE `mitos_verdades` SET 
                `pergunta` = :p,
                `resposta_correta` = :r,
                `imagem` = :img,
                `explicacao` = :exp
                WHERE `id` = :id");
            foreach ($DADOS_MITOS as $m) {
                $stmtUpdMito->execute([
                    ':id'  => (int)$m['id'],
                    ':p'   => $m['pergunta'],
                    ':r'   => $m['respostaCorreta'],
                    ':img' => $m['imagem'],
                    ':exp' => $m['explicacao']
                ]);
            }
        }
    }

} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; padding:24px; background:#FEF2F2; color:#991B1B; border:2px solid #EF4444; border-radius:12px; max-width:700px; margin:40px auto;'>
            <h2 style='margin-top:0;'>Erro de Conexão com o Banco de Dados MySQL</h2>
            <p>Certifique-se de que o <strong>MySQL</strong> está iniciado no XAMPP (utilizador <code>root</code> e palavra-passe vazia <code>\"\"</code>).</p>
            <p><strong>Mensagem técnica:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}
?>