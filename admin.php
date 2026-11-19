<?php
/**
 * PROJETO ACADÊMICO: SUSTENTA+ — Tecnologia, Informação e Sustentabilidade
 * EQUIPE 03: Alimentação Sustentável — Do campo ao prato: produzir, consumir e desperdiçar
 * ARQUIVO: admin.php (Moderação de Receitas Pendentes, Comentários e Gestão de Conteúdo)
 */

require_once 'conexao.php';

$erro_login = '';
$msg_sucesso = '';

// 1. Logout Seguro
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: admin.php");
    exit;
}

// 2. Autenticação com password_verify()
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_login'])) {
    $usuario_input = trim($_POST['usuario'] ?? '');
    $senha_input   = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM `usuarios_adm` WHERE `usuario` = :u LIMIT 1");
    $stmt->execute([':u' => $usuario_input]);
    $admin_row = $stmt->fetch();

    if ($admin_row && password_verify($senha_input, $admin_row['senha_hash'])) {
        $_SESSION['admin_logado']  = true;
        $_SESSION['admin_usuario'] = $admin_row['usuario'];
        header("Location: admin.php");
        exit;
    } else {
        $erro_login = "Usuário ou senha incorretos. Utilize 'admin' e 'admin123'.";
    }
}

// 3. Processamento das Ações de Moderação e Gestão
if (!empty($_SESSION['admin_logado']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_adm'])) {
    $acao = $_POST['acao_adm'];

    // Aprovar Receita Enviada por Visitante
    if ($acao === 'aprovar_receita') {
        $id = (int)($_POST['receita_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE `receitas` SET `status` = 'aprovado' WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_adm'] = "Receita #$id aprovada com sucesso! Ela já está publicada no site.";
        header("Location: admin.php");
        exit;
    }

    // Aprovar Comentário
    if ($acao === 'aprovar_comentario') {
        $id = (int)($_POST['comentario_id'] ?? 0);
        $stmt = $pdo->prepare("UPDATE `comentarios` SET `status` = 'aprovado' WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_adm'] = "Avaliação #$id aprovada com sucesso! Ela já está visível no site.";
        header("Location: admin.php");
        exit;
    }

    // Excluir Comentário
    if ($acao === 'excluir_comentario') {
        $id = (int)($_POST['comentario_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM `comentarios` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_adm'] = "Avaliação #$id removida permanentemente.";
        header("Location: admin.php");
        exit;
    }

    // Adicionar Novo Local Sustentável
    if ($acao === 'adicionar_local') {
        $nome       = trim($_POST['nome'] ?? '');
        $tipo       = trim($_POST['tipo'] ?? 'Feira & Ponto Sustentável');
        $cidade     = trim($_POST['cidade'] ?? 'Taubaté');
        $endereco   = trim($_POST['endereco'] ?? '');
        $descricao  = trim($_POST['descricao'] ?? '');
        $link_maps  = trim($_POST['link_maps'] ?? '');
        $imagem_url = trim($_POST['imagem_url'] ?? '');

        if ($link_maps === '' && $nome !== '') {
            $link_maps = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($nome . ' ' . $cidade . ' SP');
        }
        if ($imagem_url === '') {
            $imagem_url = 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80';
        }

        if ($nome !== '' && $endereco !== '') {
            $stmt = $pdo->prepare("INSERT INTO `locais_sustentaveis` (`nome`, `tipo`, `cidade`, `descricao`, `endereco`, `link_maps`, `imagem_url`) 
                                   VALUES (:n, :t, :c, :d, :e, :l, :img)");
            $stmt->execute([
                ':n'   => $nome,
                ':t'   => $tipo,
                ':c'   => $cidade,
                ':d'   => $descricao,
                ':e'   => $endereco,
                ':l'   => $link_maps,
                ':img' => $imagem_url
            ]);
            $_SESSION['flash_adm'] = "Local sustentável '$nome' cadastrado no mapa com sucesso!";
            header("Location: admin.php#gestao-conteudo");
            exit;
        }
    }

    // Excluir Local
    if ($acao === 'excluir_local') {
        $id = (int)($_POST['local_id'] ?? 0);
        $pdo->prepare("DELETE FROM `comentarios` WHERE `tipo_origem` = 'local' AND `item_id` = :id")->execute([':id' => $id]);
        $stmt = $pdo->prepare("DELETE FROM `locais_sustentaveis` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_adm'] = "Local #$id e suas avaliações vinculadas foram removidos.";
        header("Location: admin.php#gestao-conteudo");
        exit;
    }

    // Excluir Receita (Pendente ou Aprovada)
    if ($acao === 'excluir_receita') {
        $id = (int)($_POST['receita_id'] ?? 0);
        $pdo->prepare("DELETE FROM `comentarios` WHERE `tipo_origem` = 'receita' AND `item_id` = :id")->execute([':id' => $id]);
        $stmt = $pdo->prepare("DELETE FROM `receitas` WHERE `id` = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash_adm'] = "Receita #$id removida permanentemente.";
        header("Location: admin.php");
        exit;
    }

    // Sincronizar com dados.php
    if ($acao === 'recarregar_dados_php') {
        $pdo->exec("TRUNCATE TABLE `receitas`");
        $pdo->exec("TRUNCATE TABLE `locais_sustentaveis`");
        $pdo->exec("TRUNCATE TABLE `guia_plantio`");
        $pdo->exec("TRUNCATE TABLE `mitos_verdades`");
        $_SESSION['flash_adm'] = "Banco de dados sincronizado com o arquivo dados.php com sucesso!";
        header("Location: admin.php");
        exit;
    }
}

if (isset($_SESSION['flash_adm'])) {
    $msg_sucesso = $_SESSION['flash_adm'];
    unset($_SESSION['flash_adm']);
}

// 4. Carregamento de Indicadores e Listagens
$kpi_receitas_aprovadas = 0;
$kpi_receitas_pendentes = 0;
$kpi_locais             = 0;
$kpi_com_aprovados      = 0;
$kpi_com_pendentes      = 0;
$fila_receitas_pendentes = [];
$fila_pendentes          = [];
$lista_locais_adm        = [];
$lista_receitas_adm      = [];
$nomes_receitas          = [];
$nomes_locais            = [];

if (!empty($_SESSION['admin_logado'])) {
    $kpi_receitas_aprovadas = (int)$pdo->query("SELECT COUNT(*) FROM `receitas` WHERE `status` = 'aprovado'")->fetchColumn();
    $kpi_receitas_pendentes = (int)$pdo->query("SELECT COUNT(*) FROM `receitas` WHERE `status` = 'pendente'")->fetchColumn();
    $kpi_locais             = (int)$pdo->query("SELECT COUNT(*) FROM `locais_sustentaveis`")->fetchColumn();
    $kpi_com_aprovados      = (int)$pdo->query("SELECT COUNT(*) FROM `comentarios` WHERE `status` = 'aprovado'")->fetchColumn();
    $kpi_com_pendentes      = (int)$pdo->query("SELECT COUNT(*) FROM `comentarios` WHERE `status` = 'pendente'")->fetchColumn();

    $fila_receitas_pendentes = $pdo->query("SELECT * FROM `receitas` WHERE `status` = 'pendente' ORDER BY `id` DESC")->fetchAll();

    $lista_receitas_adm = $pdo->query("SELECT * FROM `receitas` ORDER BY `id` DESC")->fetchAll();
    foreach ($lista_receitas_adm as $r) {
        $nomes_receitas[$r['id']] = $r['titulo'];
    }

    $lista_locais_adm = $pdo->query("SELECT * FROM `locais_sustentaveis` ORDER BY `id` DESC")->fetchAll();
    foreach ($lista_locais_adm as $l) {
        $nomes_locais[$l['id']] = $l['nome'] . ' (' . $l['cidade'] . ')';
    }

    $fila_pendentes = $pdo->query("SELECT * FROM `comentarios` WHERE `status` = 'pendente' ORDER BY `data_comentario` DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | SUSTENTA+ Equipe 03</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        laranja: { 50: '#FFF7ED', 100: '#FFEDD5', 500: '#F97316', 600: '#EA580C', 700: '#C2410C' },
                        oliva: { 50: '#F7FEE7', 100: '#ECFCCB', 600: '#4D7C0F', 700: '#3F6212', 800: '#365314', 900: '#1A2E05' },
                        creme: '#FAF8F5',
                        grafite: '#1C1917'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-creme text-grafite min-h-screen flex flex-col">

    <!-- Cabeçalho Administrativo -->
    <header class="bg-oliva-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-laranja-600 flex items-center justify-center text-white">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <span class="font-display font-bold text-base">SUSTENTA+ <span class="text-laranja-400">PAINEL ADM</span></span>
                    <span class="hidden sm:inline-block text-xs text-stone-300 ml-2">• Equipe 03 (Alimentação Sustentável)</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Voltar ao Site
                </a>
                <?php if (!empty($_SESSION['admin_logado'])): ?>
                    <a href="admin.php?logout=1" class="text-xs font-bold bg-red-600 hover:bg-red-700 px-4 py-2 rounded-xl transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket"></i> Sair
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if (empty($_SESSION['admin_logado'])): ?>
        <!-- TELA DE LOGIN DO ADMINISTRADOR -->
        <main class="flex-1 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl shadow-xl border border-stone-200 max-w-md w-full p-8 animate-fade-in">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-oliva-700 text-white flex items-center justify-center text-2xl mx-auto mb-3 shadow-md">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h1 class="font-display text-2xl font-extrabold text-grafite">Acesso Administrativo</h1>
                    <p class="text-xs text-stone-500 mt-1">Moderação de receitas, avaliações e gerenciamento de conteúdo</p>
                </div>

                <div class="bg-laranja-50 border border-laranja-200 rounded-2xl p-4 mb-6 text-xs text-stone-700">
                    <div class="font-extrabold text-laranja-700 uppercase mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-key"></i> Credenciais de Acesso:
                    </div>
                    <p>Usuário: <code class="bg-white px-2 py-0.5 rounded border font-bold">admin</code> • Senha: <code class="bg-white px-2 py-0.5 rounded border font-bold">admin123</code></p>
                </div>

                <?php if ($erro_login !== ''): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold p-3.5 rounded-xl mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars($erro_login) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="admin.php" class="space-y-4">
                    <input type="hidden" name="acao_login" value="1">
                    <div>
                        <label class="block text-xs font-bold uppercase text-stone-600 mb-1">Usuário</label>
                        <input type="text" name="usuario" value="admin" required class="w-full p-3 rounded-xl border border-stone-300 text-sm focus:outline-none focus:border-laranja-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-stone-600 mb-1">Senha</label>
                        <input type="password" name="senha" value="admin123" required class="w-full p-3 rounded-xl border border-stone-300 text-sm focus:outline-none focus:border-laranja-600">
                    </div>
                    <button type="submit" class="w-full bg-laranja-600 hover:bg-laranja-700 text-white font-bold py-3.5 rounded-xl shadow transition-colors text-sm">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Entrar no Painel
                    </button>
                </form>
            </div>
        </main>

    <?php else: ?>
        <!-- DASHBOARD ADMINISTRATIVO COMPLETO -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

            <?php if ($msg_sucesso !== ''): ?>
                <div class="bg-oliva-700 text-white p-4 rounded-2xl shadow flex items-center gap-3 text-sm font-semibold">
                    <i class="fa-solid fa-circle-check text-laranja-400 text-lg"></i>
                    <span><?= htmlspecialchars($msg_sucesso) ?></span>
                </div>
            <?php endif; ?>

            <!-- Visão Geral do Sistema -->
            <section>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-grafite">
                        Visão Geral do Sistema
                    </h1>
                    <form method="POST" action="admin.php" onsubmit="return confirm('Deseja recarregar as receitas, locais e 30 questões padrão a partir do arquivo dados.php?');">
                        <input type="hidden" name="acao_adm" value="recarregar_dados_php">
                        <button type="submit" class="text-xs font-bold bg-white hover:bg-stone-100 text-stone-700 border border-stone-300 px-4 py-2.5 rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-arrows-rotate text-laranja-600"></i> Sincronizar com dados.php
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white p-5 rounded-3xl shadow-sm border border-stone-200">
                        <span class="text-xs font-extrabold uppercase text-oliva-700">Receitas no Site</span>
                        <p class="font-display text-3xl font-extrabold text-grafite mt-2"><?= $kpi_receitas_aprovadas ?></p>
                    </div>
                    <div class="bg-laranja-50 p-5 rounded-3xl shadow-sm border-2 border-laranja-500">
                        <span class="text-xs font-extrabold uppercase text-laranja-700">Receitas Pendentes</span>
                        <p class="font-display text-3xl font-extrabold text-laranja-600 mt-2"><?= $kpi_receitas_pendentes ?></p>
                    </div>
                    <div class="bg-white p-5 rounded-3xl shadow-sm border border-stone-200">
                        <span class="text-xs font-extrabold uppercase text-oliva-700">Locais no Mapa</span>
                        <p class="font-display text-3xl font-extrabold text-grafite mt-2"><?= $kpi_locais ?></p>
                    </div>
                    <div class="bg-white p-5 rounded-3xl shadow-sm border border-stone-200">
                        <span class="text-xs font-extrabold uppercase text-emerald-700">Avaliações Aprovadas</span>
                        <p class="font-display text-3xl font-extrabold text-grafite mt-2"><?= $kpi_com_aprovados ?></p>
                    </div>
                    <div class="bg-laranja-50 p-5 rounded-3xl shadow-sm border-2 border-laranja-500 col-span-2 lg:col-span-1">
                        <span class="text-xs font-extrabold uppercase text-laranja-700">Avaliações Pendentes</span>
                        <p class="font-display text-3xl font-extrabold text-laranja-600 mt-2"><?= $kpi_com_pendentes ?></p>
                    </div>
                </div>
            </section>

            <!-- NOVA SEÇÃO: RECEITAS ENVIADAS AGUARDANDO APROVAÇÃO -->
            <section class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border-2 border-laranja-500/40">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider bg-laranja-100 text-laranja-700 px-3 py-1 rounded-full">
                            Moderação de Receitas
                        </span>
                        <h2 class="font-display text-2xl font-extrabold text-grafite mt-2">
                            Receitas Enviadas Aguardando Aprovação (<?= count($fila_receitas_pendentes) ?>)
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Revise as receitas compartilhadas pelos visitantes antes de publicá-las no catálogo oficial do site.</p>
                    </div>
                </div>

                <?php if (empty($fila_receitas_pendentes)): ?>
                    <div class="bg-oliva-50 border border-oliva-200 rounded-2xl p-6 text-center text-xs text-oliva-900 font-semibold">
                        <i class="fa-solid fa-circle-check text-oliva-700 mr-1"></i> Nenhuma receita pendente de aprovação no momento!
                    </div>
                <?php else: ?>
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php foreach ($fila_receitas_pendentes as $rp): ?>
                        <div class="bg-creme rounded-2xl p-5 border border-stone-200 flex flex-col justify-between gap-4">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div>
                                        <span class="text-[10px] font-extrabold uppercase bg-oliva-700 text-white px-2.5 py-0.5 rounded-full">
                                            <?= htmlspecialchars($rp['categoria']) ?>
                                        </span>
                                        <h3 class="font-display font-bold text-lg text-grafite mt-1"><?= htmlspecialchars($rp['titulo']) ?></h3>
                                        <p class="text-xs text-stone-500">Enviada por: <strong><?= htmlspecialchars($rp['autor']) ?></strong></p>
                                    </div>
                                    <img src="<?= htmlspecialchars($rp['imagem_url']) ?>" alt="Preview" class="w-16 h-16 rounded-xl object-cover border border-stone-300 shrink-0">
                                </div>

                                <div class="space-y-2 text-xs text-stone-700 bg-white p-3.5 rounded-xl border border-stone-200">
                                    <p><strong>Ingredientes:</strong> <?= htmlspecialchars($rp['ingredientes']) ?></p>
                                    <p><strong>Modo de Preparo:</strong> <?= htmlspecialchars($rp['modo_preparo']) ?></p>
                                    <p class="text-oliva-800"><strong>Dica Sustentável:</strong> <?= htmlspecialchars($rp['dica_sustentavel']) ?></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                                <form method="POST" action="admin.php" class="inline">
                                    <input type="hidden" name="acao_adm" value="aprovar_receita">
                                    <input type="hidden" name="receita_id" value="<?= (int)$rp['id'] ?>">
                                    <button type="submit" class="bg-oliva-700 hover:bg-oliva-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors inline-flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-check"></i> Aprovar e Publicar no Site
                                    </button>
                                </form>
                                <form method="POST" action="admin.php" class="inline" onsubmit="return confirm('Deseja recusar e excluir esta receita enviada?');">
                                    <input type="hidden" name="acao_adm" value="excluir_receita">
                                    <input type="hidden" name="receita_id" value="<?= (int)$rp['id'] ?>">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors inline-flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-trash"></i> Recusar / Excluir
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Comentários Aguardando Aprovação -->
            <section class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-display text-2xl font-extrabold text-grafite">
                            Comentários Aguardando Aprovação (<?= count($fila_pendentes) ?>)
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Aprove ou remova as avaliações enviadas pelos visitantes do site.</p>
                    </div>
                </div>

                <?php if (empty($fila_pendentes)): ?>
                    <div class="bg-oliva-50 border border-oliva-200 rounded-2xl p-6 text-center text-xs text-oliva-900 font-semibold">
                        <i class="fa-solid fa-circle-check text-oliva-700 mr-1"></i> Nenhum comentário pendente no momento. Todas as avaliações já foram revisadas!
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-stone-200 text-[11px] font-extrabold uppercase text-stone-500 bg-stone-50">
                                    <th class="p-3.5">Visitante</th>
                                    <th class="p-3.5">Item Avaliado</th>
                                    <th class="p-3.5">Avaliação</th>
                                    <th class="p-3.5">Comentário</th>
                                    <th class="p-3.5 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 text-xs">
                                <?php foreach ($fila_pendentes as $c):
                                    $item_nome = ($c['tipo_origem'] === 'receita')
                                        ? ($nomes_receitas[$c['item_id']] ?? 'Receita #' . $c['item_id'])
                                        : ($nomes_locais[$c['item_id']] ?? 'Local #' . $c['item_id']);
                                    $data_formatada = date('d/m/Y \à\s H:i', strtotime($c['data_comentario']));
                                ?>
                                <tr class="hover:bg-laranja-50/40">
                                    <td class="p-3.5 font-bold text-grafite">
                                        <?= htmlspecialchars($c['nome_usuario']) ?>
                                        <span class="block text-[10px] font-normal text-stone-400 mt-0.5"><?= htmlspecialchars($data_formatada) ?></span>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="uppercase text-[10px] font-extrabold bg-stone-200 text-stone-700 px-2 py-0.5 rounded mr-1">
                                            <?= $c['tipo_origem'] === 'receita' ? 'Receita' : 'Local' ?>
                                        </span>
                                        <strong class="text-grafite"><?= htmlspecialchars($item_nome) ?></strong>
                                    </td>
                                    <td class="p-3.5 text-amber-500 font-bold whitespace-nowrap">
                                        <?= str_repeat('★', (int)$c['nota']) ?> <span class="text-stone-500">(<?= (int)$c['nota'] ?>/5)</span>
                                    </td>
                                    <td class="p-3.5 text-stone-700 max-w-md">"<?= htmlspecialchars($c['texto']) ?>"</td>
                                    <td class="p-3.5 text-right whitespace-nowrap space-x-1.5">
                                        <form method="POST" action="admin.php" class="inline">
                                            <input type="hidden" name="acao_adm" value="aprovar_comentario">
                                            <input type="hidden" name="comentario_id" value="<?= (int)$c['id'] ?>">
                                            <button type="submit" class="bg-oliva-700 hover:bg-oliva-800 text-white font-bold px-3.5 py-2 rounded-xl transition-colors inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-check"></i> Aprovar
                                            </button>
                                        </form>
                                        <form method="POST" action="admin.php" class="inline" onsubmit="return confirm('Confirmar exclusão deste comentário?');">
                                            <input type="hidden" name="acao_adm" value="excluir_comentario">
                                            <input type="hidden" name="comentario_id" value="<?= (int)$c['id'] ?>">
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2 rounded-xl transition-colors inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-trash"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Gerenciamento de Conteúdo -->
            <section id="gestao-conteudo" class="grid lg:grid-cols-12 gap-8">
                <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-stone-200">
                    <h2 class="font-display text-xl font-extrabold text-grafite mb-4">
                        Adicionar Novo Local Sustentável no Mapa
                    </h2>
                    <form method="POST" action="admin.php" class="space-y-3.5 text-xs">
                        <input type="hidden" name="acao_adm" value="adicionar_local">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Nome do Local *</label>
                            <input type="text" name="nome" required placeholder="Ex: Feira Agroecológica de Quiririm" class="w-full p-2.5 rounded-xl border border-stone-300">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Tipo *</label>
                                <input type="text" name="tipo" required placeholder="Ex: Feira Orgânica" class="w-full p-2.5 rounded-xl border border-stone-300">
                            </div>
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Cidade *</label>
                                <select name="cidade" class="w-full p-2.5 rounded-xl border border-stone-300 bg-white font-semibold">
                                    <option value="Taubaté">Taubaté</option>
                                    <option value="Tremembé">Tremembé</option>
                                    <option value="Pindamonhangaba">Pindamonhangaba</option>
                                    <option value="São José dos Campos">São José dos Campos</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Endereço *</label>
                            <input type="text" name="endereco" required placeholder="Rua, número e bairro" class="w-full p-2.5 rounded-xl border border-stone-300">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Descrição *</label>
                            <textarea name="descricao" rows="2" required placeholder="Detalhes sobre os produtos e impacto sustentável..." class="w-full p-2.5 rounded-xl border border-stone-300"></textarea>
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">URL da Foto do Local (Opcional)</label>
                            <input type="url" name="imagem_url" placeholder="https://..." class="w-full p-2.5 rounded-xl border border-stone-300">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Link Google Maps (Opcional)</label>
                            <input type="url" name="link_maps" placeholder="https://www.google.com/maps/search/?api=1&query=..." class="w-full p-2.5 rounded-xl border border-stone-300">
                        </div>
                        <button type="submit" class="w-full bg-laranja-600 hover:bg-laranja-700 text-white font-bold py-3 rounded-xl shadow">
                            <i class="fa-solid fa-plus-circle mr-1"></i> Cadastrar Local no Mapa
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <!-- Exclusão Rápida de Locais -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                        <h3 class="font-display font-bold text-lg text-grafite mb-3">Locais Cadastrados no Mapa (<?= count($lista_locais_adm) ?>)</h3>
                        <div class="divide-y divide-stone-200 max-h-60 overflow-y-auto pr-2">
                            <?php foreach ($lista_locais_adm as $l): ?>
                                <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <strong class="text-grafite"><?= htmlspecialchars($l['nome']) ?></strong>
                                        <span class="text-stone-500 block"><?= htmlspecialchars($l['tipo']) ?> • <?= htmlspecialchars($l['cidade']) ?></span>
                                    </div>
                                    <form method="POST" action="admin.php" onsubmit="return confirm('Excluir este local?');">
                                        <input type="hidden" name="acao_adm" value="excluir_local">
                                        <input type="hidden" name="local_id" value="<?= (int)$l['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg font-bold">
                                            <i class="fa-solid fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Exclusão Rápida de Receitas -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-stone-200">
                        <h3 class="font-display font-bold text-lg text-grafite mb-3">Todas as Receitas no Banco (<?= count($lista_receitas_adm) ?>)</h3>
                        <div class="divide-y divide-stone-200 max-h-60 overflow-y-auto pr-2">
                            <?php foreach ($lista_receitas_adm as $r): ?>
                                <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded mr-1 <?= $r['status'] === 'aprovado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                            <?= $r['status'] === 'aprovado' ? 'Publicada' : 'Pendente' ?>
                                        </span>
                                        <strong class="text-grafite"><?= htmlspecialchars($r['titulo']) ?></strong>
                                        <span class="text-stone-500 block"><?= htmlspecialchars($r['categoria']) ?> • Fonte: <?= htmlspecialchars($r['autor']) ?></span>
                                    </div>
                                    <form method="POST" action="admin.php" onsubmit="return confirm('Excluir esta receita?');">
                                        <input type="hidden" name="acao_adm" value="excluir_receita">
                                        <input type="hidden" name="receita_id" value="<?= (int)$r['id'] ?>">
                                        <button type="submit" class="text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg font-bold">
                                            <i class="fa-solid fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    <?php endif; ?>

</body>
</html>